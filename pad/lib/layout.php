<?php

  // Layouts and named blocks, resolved while the page is assembled - build/page.php calls
  // padLayout with the page's own template and the frame round it, before anything renders.
  //
  //   {extends 'layouts/report'}             layouts/report.pad
  //   {block 'title'}Sales{/block}           <title>{block 'title'}Reports{/block}</title>
  //   {block 'sidebar'}                      {block 'sidebar'}<a href="?home">Home</a>{/block}
  //     {parent} <a href="?x">Export</a>     @page@
  //   {/block}
  //   <p>the rest of the page</p>
  //
  // A layout is a template named from the application root, like a {page}. A page that
  // extends one is framed by it instead of by its directories' _inits.pad and _exits.pad
  // (and the _common wrapper): the layout is the page's frame. What the page holds outside
  // its blocks goes where the layout writes @page@ - in front of it when there is none. A
  // layout may extend a layout of its own.
  //
  // A {block 'name'} standing directly in the page - not inside another tag - overrides the
  // block of that name in the frame; {parent} inside it is the content it overrides, so a
  // page can add to a region instead of replacing it. Any other {block 'name'} is a region:
  // its content renders, unless something overrides it. The blocks of the directories'
  // wrappers are regions as well, so a page without {extends} sets its wrapper's title with
  // {block 'title'}...{/block} - a block of the page that overrides nothing stays where it
  // is. Under the strict check, a block of a page that extends a layout must override one.
  //
  // It all happens in the text, so it costs nothing at render time and the tags are gone
  // before the page renders: {block 'name'} needs its name quoted, which also keeps the
  // {block} snippet of the _common application - no name - what it was. What stands
  // between {ignore} tags is left alone.

  function padLayout ( &$template, &$frame ) {

    if ( ! str_contains ( $template . $frame, '{extends' )
         and ! str_contains ( $template . $frame, '{block' )
         and ! str_contains ( $template . $frame, '{parent' ) )
      return;

    $masks = [];
    $text  = padLayoutMask ( $template, $masks );

    $extends = padLayoutExtends ( $text );

    if ( $extends !== FALSE ) {

      $frame    = '@page@';
      $template = padLayoutUnmask ( padLayoutChain ( $text, $extends, $masks ), $masks );

      return;

    }

    $defs  = padLayoutDefs ( $text, 'the page' );
    $used  = [];
    $outer = padLayoutFill ( padLayoutMask ( $frame, $masks ), $defs, $used );

    foreach ( array_reverse ( padLayoutBlocks ( $text, TRUE ) ) as $pair )
      if ( isset ( $used [ $pair ['name'] ] ) )
        $text = substr ( $text, 0, $pair ['start'] ) . substr ( $text, $pair ['end'] );

    $none     = [];
    $frame    = padLayoutUnmask ( $outer, $masks );
    $template = padLayoutUnmask ( padLayoutFill ( $text, [], $none ), $masks );

  }

  // The page and the layouts it extends, most derived first, each loaded and its {extends}
  // taken out: their top-level blocks become the overrides, what is left of each goes into
  // the @page@ of the one it extends, and the blocks of the result are resolved.

  function padLayoutChain ( $text, $extends, &$masks ) {

    $chain = [ [ 'text' => $extends ['text'], 'name' => 'the page' ] ];
    $seen  = [];
    $name  = $extends ['name'];

    while ( TRUE ) {

      if ( isset ( $seen [$name] ) or count ( $seen ) > 20 ) {
        padError ( "the layouts extend one another in a loop - '$name' again" );
        break;
      }

      $seen [$name] = TRUE;

      $source = padLayoutLoad ( $name );

      if ( $source === FALSE ) {
        if ( $GLOBALS ['padCheckSyntax'] )
          padError ( "there is no layout named '$name' - {extends} names a template from the application root" );
        $source = '@page@';
      }

      $layout = padLayoutMask ( $source, $masks );
      $next   = padLayoutExtends ( $layout );

      if ( $next === FALSE ) {
        $chain [] = [ 'text' => $layout, 'name' => $name ];
        break;
      }

      $chain [] = [ 'text' => $next ['text'], 'name' => $name ];
      $name     = $next ['name'];

    }

    $root = array_pop ( $chain );
    $defs = [];

    foreach ( $chain as $one ) {

      $blocks = padLayoutBlocks ( $one ['text'], TRUE );

      foreach ( padLayoutDefs ( $one ['text'], $one ['name'] ) as $block => $list )
        $defs [$block] [] = [ 'content' => $list [0] ['content'], 'from' => $one ['name'] ];

      $rest = $one ['text'];

      foreach ( array_reverse ( $blocks ) as $pair )
        $rest = substr ( $rest, 0, $pair ['start'] ) . substr ( $rest, $pair ['end'] );

      $rests [] = $rest;

    }

    $composed = $root ['text'];

    foreach ( array_reverse ( $rests ?? [] ) as $rest )
      if ( str_contains ( $composed, '@page@' ) )
        $composed = preg_replace ( '/@page@/', str_replace ( [ '\\', '$' ], [ '\\\\', '\\$' ], $rest ), $composed, 1 );
      elseif ( trim ( $rest ) !== '' )
        $composed = trim ( $rest ) . $composed;

    $used     = [];
    $composed = padLayoutFill ( $composed, $defs, $used, TRUE );

    if ( $GLOBALS ['padCheckSyntax'] )
      foreach ( $defs as $block => $list )
        if ( ! isset ( $used [$block] ) )
          padError ( "the block '$block' of " . $list [0] ['from'] . " overrides nothing - '" . $root ['name'] . "' has no block of that name" );

    return $composed;

  }

  // The layout's template - .pad, else .html - named from the application root.

  function padLayoutLoad ( $name ) {

    $name = trim ( (string) $name, '/ ' );

    if ( $name === '' or str_contains ( $name, '..' ) or ! preg_match ( '/^[A-Za-z0-9_\/.-]+$/', $name ) )
      return FALSE;

    if ( file_exists ( APP . "$name.pad" ) )  return padFileGet ( APP . "$name.pad" );
    if ( file_exists ( APP . "$name.html" ) ) return padFileGet ( APP . "$name.html" );

    return FALSE;

  }

  // The {extends 'name'} standing directly in a template: its name and the template without
  // it, or FALSE when there is none.

  function padLayoutExtends ( $text ) {

    $offset = 0;

    while ( ( $pos = padPairNext ( $text, '{extends', $offset ) ) !== FALSE ) {

      $end = padPairTagEnd ( $text, $pos + 8 );

      if ( $end === FALSE )
        return FALSE;

      if ( padPairTopLevel ( $text, $pos ) ) {

        $parms = rtrim ( trim ( substr ( $text, $pos + 8, $end - $pos - 8 ) ), '/' );
        $name  = padPairName ( $parms );
        $rest  = substr ( $text, 0, $pos ) . substr ( $text, $end + 1 );

        if ( $GLOBALS ['padCheckSyntax'] and padLayoutExtends ( $rest ) !== FALSE )
          padError ( "a page extends one layout - this one has two {extends}" );

        if ( $name === '' and $GLOBALS ['padCheckSyntax'] )
          padError ( "the {extends} needs the name of a layout - {extends 'layouts/report'}" );

        return [ 'name' => $name, 'text' => $rest ];

      }

      $offset = $end + 1;

    }

    return FALSE;

  }

  // The {block 'name'} pairs of a text that are not inside another named block, with their
  // names - only those standing directly in the text when $top is set. A name must be
  // quoted: a {block} without one is not a layout block.

  function padLayoutBlocks ( $text, $top = FALSE ) {

    $named = [];

    foreach ( padPairScan ( $text, 'block' ) as $pair ) {

      if ( $pair ['single'] or ! preg_match ( '/^([\'"])([^\'"]+)\1$/', trim ( padExplode ( $pair ['parms'], ',' ) [0] ?? '' ), $match ) )
        continue;

      $pair ['name'] = $match [2];
      $named []      = $pair;

    }

    $outer = [];

    foreach ( $named as $pair ) {

      foreach ( $outer as $one )
        if ( $pair ['start'] > $one ['start'] and $pair ['end'] <= $one ['end'] )
          continue 2;

      if ( $top and ! padPairTopLevel ( $text, $pair ['start'] ) )
        continue;

      $outer [] = $pair;

    }

    return $outer;

  }

  // The overrides a template defines: its top-level blocks by name.

  function padLayoutDefs ( $text, $from ) {

    $defs = [];

    foreach ( padLayoutBlocks ( $text, TRUE ) as $pair ) {

      if ( isset ( $defs [ $pair ['name'] ] ) and $GLOBALS ['padCheckSyntax'] )
        padError ( "the block '" . $pair ['name'] . "' stands twice in $from" );

      $defs [ $pair ['name'] ] = [ [ 'content' => substr ( $text, $pair ['inner'], $pair ['close'] - $pair ['inner'] ), 'from' => $from ] ];

    }

    return $defs;

  }

  // Replaces every named block of a text by what it stands for: the most derived override,
  // its {parent} the next one down, the last one the block's own content - and so on for
  // the blocks inside what comes out. $used collects the names met.
  //
  // $open holds the blocks being filled round the text. A block met again inside what it
  // is filled with - {block 'title'} inside the override of 'title', or 'a' in the override
  // of 'b' and 'b' in that of 'a' - keeps its own content there: filled again it was
  // filled without end, and the request died on PHP's call stack.

  function padLayoutFill ( $text, $defs, &$used, $strict = FALSE, $open = [] ) {

    foreach ( array_reverse ( padLayoutBlocks ( $text ) ) as $pair ) {

      $name    = $pair ['name'];
      $default = substr ( $text, $pair ['inner'], $pair ['close'] - $pair ['inner'] );

      $used [$name] = TRUE;

      if ( isset ( $open [$name] ) ) {
        if ( $GLOBALS ['padCheckSyntax'] )
          padError ( "the block '$name' stands inside what overrides it" );
        $content = padLayoutParent ( $default, '' );
      }
      else
        $content = padLayoutContent ( $defs [$name] ?? [], 0, $default, $strict );

      $content = padLayoutFill ( $content, $defs, $used, $strict, $open + [ $name => TRUE ] );

      $text = substr ( $text, 0, $pair ['start'] ) . $content . substr ( $text, $pair ['end'] );

    }

    return $text;

  }

  // A {parent} with nothing under it is empty. In a chain of layouts that is a mistake the
  // strict check names; a page in its directories' frame is also fetched without the frame
  // - padInclude, a {page} - and its block then rightly stands alone.

  function padLayoutContent ( $list, $index, $default, $strict ) {

    if ( ! isset ( $list [$index] ) ) {

      if ( $strict and padLayoutParentAt ( $default ) and $GLOBALS ['padCheckSyntax'] )
        padError ( "a {parent} stands in a block that overrides nothing" );

      return padLayoutParent ( $default, '' );

    }

    return padLayoutParent ( $list [$index] ['content'], padLayoutContent ( $list, $index + 1, $default, $strict ) );

  }

  // The {parent} tags of a block's own content - not those of a block nested in it, which
  // have a parent of their own.

  function padLayoutParentAt ( $text ) {

    return padLayoutParentList ( $text ) !== [];

  }

  function padLayoutParent ( $text, $parent ) {

    foreach ( array_reverse ( padLayoutParentList ( $text ) ) as [ $start, $end ] )
      $text = substr ( $text, 0, $start ) . $parent . substr ( $text, $end );

    return $text;

  }

  function padLayoutParentList ( $text ) {

    if ( ! str_contains ( $text, '{parent' ) )
      return [];

    $nested = padLayoutBlocks ( $text );
    $list   = [];

    if ( preg_match_all ( '/\{parent\s*\/?\}/', $text, $matches, PREG_OFFSET_CAPTURE ) )
      foreach ( $matches [0] as [ $match, $offset ] ) {
        foreach ( $nested as $pair )
          if ( $offset > $pair ['start'] and $offset < $pair ['end'] )
            continue 2;
        $list [] = [ $offset, $offset + strlen ( $match ) ];
      }

    return $list;

  }

  // What stands between {ignore} tags is set aside while the blocks are resolved and put
  // back after.

  function padLayoutMask ( $text, &$masks ) {

    return preg_replace_callback ( '/\{ignore\}.*?\{\/ignore\}/s', function ( $match ) use ( &$masks ) {
      $masks [] = $match [0];
      return "\u{E0F2}" . ( count ( $masks ) - 1 ) . "\u{E0F3}";
    }, (string) $text );

  }

  function padLayoutUnmask ( $text, $masks ) {

    return preg_replace_callback ( '/\x{E0F2}(\d+)\x{E0F3}/u', fn ( $match ) => $masks [ (int) $match [1] ] ?? '', $text );

  }

?>
