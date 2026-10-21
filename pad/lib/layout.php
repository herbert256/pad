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

    $template = padLayoutTilde ( $template );
    $frame    = padLayoutTilde ( $frame );

    if ( ! str_contains ( $template . $frame, '{extends' )
         and ! str_contains ( $template . $frame, '{block' )
         and ! str_contains ( $template . $frame, '{parent' ) )
      return;

    $masks = [];
    $text  = padLayoutMask ( $template, $masks );

    $extends = padLayoutExtends ( $text );

    // The composed layout is the frame, with one @page@ where the page's rest goes, and that
    // rest is the template - as with the _inits.pad and _exits.pad it replaces. Handed back
    // whole as the template, with '@page@' for a frame, the layout was all the page's: a page
    // whose PHP returned two rows rendered two <html> documents, one per row, and what its
    // PHP echoed stood in front of the <html>.

    if ( $extends !== FALSE ) {

      $page     = '';
      $frame    = padLayoutNoted ( padLayoutUnmask ( padLayoutChain ( $text, $extends, $masks, $page ), $masks ) );
      $template = padLayoutUnmask ( $page, $masks );

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
  // taken out: their top-level blocks become the overrides, the blocks of the root layout
  // are resolved, and what is left of each goes into the @page@ of the one it extends.
  //
  // The blocks are filled first and the rests placed after, each @page@ marked with the
  // layout that wrote it: a layout writes its @page@ inside a block it overrides as often
  // as outside one - base.pad {block 'body'}@page@{/block}, its section layout overriding
  // 'body' with <aside>...</aside><main>@page@</main>. Placed before the blocks were
  // filled, the section's rest took the base's @page@ in the default 'body' loses, the
  // page's rest found none left and went in front of <html>, and the section's own @page@
  // came out as the text "@page@". A rest goes into the first @page@ that is left of the
  // layout it extends - or of the one that layout extends, when it has none - and a rest
  // of nothing but white space, a layout of blocks only, leaves that @page@ to the next.

  // The page's own rest is no part of what this composes: it comes back in $page, and a mark
  // stands where it goes, which becomes the frame's one @page@.

  function padLayoutChain ( $text, $extends, &$masks, &$page ) {

    $mark = "\u{E0FC}";

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

    $root  = array_pop ( $chain );
    $last  = count ( $chain );
    $defs  = [];
    $rests = [];
    $used  = [];

    foreach ( $chain as $level => $one ) {

      $own    = padLayoutHoles ( $one ['text'], $level );
      $blocks = padLayoutBlocks ( $own, TRUE );

      foreach ( padLayoutDefs ( $own, $one ['name'] ) as $block => $list )
        $defs [$block] [] = [ 'content' => $list [0] ['content'], 'from' => $one ['name'] ];

      foreach ( array_reverse ( $blocks ) as $pair )
        $own = substr ( $own, 0, $pair ['start'] ) . substr ( $own, $pair ['end'] );

      $rests [$level] = $own;

    }

    $composed = padLayoutFill ( padLayoutHoles ( $root ['text'], $last ), $defs, $used, TRUE );

    for ( $level = $last - 1; $level >= 0; $level-- ) {

      $rest = padLayoutFill ( $rests [$level], $defs, $used, TRUE );
      $hole = FALSE;

      if ( $level == 0 ) {
        $page = $rest;
        $rest = $mark;
      }

      for ( $up = $level + 1; $up <= $last and $hole === FALSE; $up++ )
        if ( str_contains ( $composed, padLayoutHole ( $up ) ) )
          $hole = padLayoutHole ( $up );

      if ( $hole !== FALSE ) {
        if ( padLayoutBlank ( $rest, $masks ) )
          $rest .= $hole;
        $at       = strpos ( $composed, $hole );
        $composed = substr ( $composed, 0, $at ) . $rest . substr ( $composed, $at + strlen ( $hole ) );
      }
      elseif ( trim ( $rest ) !== '' ) {
        $composed = trim ( $rest ) . $composed;
        if ( $level == 0 )
          $page = trim ( $page );
      }

    }

    // A @page@ nothing went into is empty - the page's own stays the text it was: in its rest
    // as it stands, in a block it overrides as text the frame's @page@ is not, the @ as its
    // stand-in, which the page's exit turns back.

    $page     = str_replace ( padLayoutHole ( 0 ), '@page@', $page );
    $page     = preg_replace ( '/\x{E0FA}\d+\x{E0FB}/u', '', $page );
    $composed = str_replace ( padLayoutHole ( 0 ), strtr ( '@page@', [ '@' => padProtectMap () ['@'] ] ), $composed );
    $composed = preg_replace ( '/\x{E0FA}\d+\x{E0FB}/u', '', $composed );
    $composed = str_replace ( $mark, '@page@', $composed );

    if ( $GLOBALS ['padCheckSyntax'] )
      foreach ( $defs as $block => $list )
        if ( ! isset ( $used [$block] ) )
          padError ( "the block '$block' of " . $list [0] ['from'] . " overrides nothing - '" . $root ['name'] . "' has no block of that name" );

    return $composed;

  }

  // Where a page's own @else@ stands - the first one with every tag pair in front of it
  // closed, found as build/split.php finds it - or FALSE when there is none.

  function padLayoutElse ( $text ) {

    if ( ! str_contains ( (string) $text, '@else@' ) )
      return FALSE;

    $marked = padMarkerText ( $text );
    $tags   = padOpenCloseList ( $marked );

    for ( $pos = strpos ( $marked, '@else@' ); $pos !== FALSE; $pos = strpos ( $marked, '@else@', $pos + 1 ) )
      if ( padOpenCloseCount ( substr ( $marked, 0, $pos ), $tags ) )
        return $pos;

    return FALSE;

  }

  // How long the run of a page's first lines is that holds nothing but its {meta},
  // {extends} and {block} pairs, with white space and comments between them - what stands in
  // front of the page's content, its own for both halves of an @else@ (build/page.php).

  function padLayoutLead ( $text ) {

    $pos    = 0;
    $blocks = NULL;

    while ( TRUE ) {

      $pos += strspn ( $text, " \t\r\n", $pos );

      foreach ( [ '{--', '{#' ] as $open )
        if ( substr ( $text, $pos, strlen ( $open ) ) === $open and ( $end = padLayoutComment ( $text, $pos, $open ) ) !== FALSE ) {
          $pos = $end;
          continue 2;
        }

      if ( preg_match ( '/\G\{(meta|extends)[\s}\/]/', $text, $match, 0, $pos ) ) {
        $end = padPairTagEnd ( $text, $pos + 1 );
        if ( $end === FALSE )
          break;
        $pos = $end + 1;
        continue;
      }

      if ( substr ( $text, $pos, 6 ) === '{block' ) {
        $blocks ??= padLayoutBlocks ( $text, TRUE );
        foreach ( $blocks as $pair )
          if ( $pair ['start'] == $pos ) {
            $pos = $pair ['end'];
            continue 2;
          }
      }

      break;

    }

    return $pos;

  }

  // That run for the @else@ half: without the {block}s the half writes of its own, and
  // without its {extends} when the half has one.

  function padLayoutLeadFor ( $lead, $half ) {

    $own = [];

    foreach ( padLayoutBlocks ( $half, TRUE ) as $pair )
      $own [ $pair ['name'] ] = TRUE;

    foreach ( array_reverse ( padLayoutBlocks ( $lead, TRUE ) ) as $pair )
      if ( isset ( $own [ $pair ['name'] ] ) )
        $lead = substr ( $lead, 0, $pair ['start'] ) . substr ( $lead, $pair ['end'] );

    if ( padLayoutExtends ( $half ) !== FALSE and ( $extends = padLayoutExtends ( $lead ) ) !== FALSE )
      $lead = $extends ['text'];

    return $lead;

  }

  // The @page@ of a template in the chain as the mark of its place there - 0 the page, 1
  // the layout it extends, and so on.

  function padLayoutHoles ( $text, $level ) {

    return str_replace ( '@page@', padLayoutHole ( $level ), $text );

  }

  function padLayoutHole ( $level ) {

    return "\u{E0FA}$level\u{E0FB}";

  }

  // Whether a rest is nothing but white space and comments - a comment is set aside by
  // padLayoutMask, and a layout opening with one explaining it holds no text of its own.

  function padLayoutBlank ( $text, $masks ) {

    $text = preg_replace_callback ( '/\x{E0F2}(\d+)\x{E0F3}/u', fn ( $match ) =>
      preg_match ( '/^\{(#|--)/', $masks [ (int) $match [1] ] ?? '' ) ? '' : $match [0], $text );

    return trim ( $text ) === '';

  }

  // The layout's template - .pad, else .html - named from the application root.

  function padLayoutLoad ( $name ) {

    $name = trim ( (string) $name, '/ ' );

    if ( $name === '' or str_contains ( $name, '..' ) or ! preg_match ( '/^[A-Za-z0-9_\/.-]+$/', $name ) )
      return FALSE;

    if ( file_exists ( APP . "$name.pad" ) )  return padLayoutTilde ( padFileGet ( APP . "$name.pad" ) );
    if ( file_exists ( APP . "$name.html" ) ) return padLayoutTilde ( padFileGet ( APP . "$name.html" ) );

    return FALSE;

  }

  // The tags the assembly reads - {block}, {extends}, {parent}, {meta} - written with
  // whitespace control, {block 'title'~}...{~/block}, are read as written without it, the
  // white space beside the ~ taken as the render takes it (padTildeStrip, which the rest of
  // the page still gets when it renders). Looked for as {block, {~block was no block: the
  // page's override landed in the body and the wrapper kept its default, and
  // {extends 'x'~} and {meta title='x'~} ended in "'~' is no operator or value". The
  // content of a {highlight} pair is code shown as it stands (padTildeKeep).

  function padLayoutTilde ( $text ) {

    if ( ! is_string ( $text ) or ! str_contains ( $text, '~' ) )
      return $text;

    $kept = [];
    $text = padTildeKeep ( $text, $kept );
    $name = '\/?(?:block|extends|parent|meta)(?=[\s}~\/])';

    $text = preg_replace ( "/\s*\{~(?=$name)/", '{', $text );
    $text = preg_replace ( "/(\{{$name}[^{}]*?)~\}\s*/", '$1}', $text );

    return $kept ? strtr ( $text, $kept ) : $text;

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
  // back after, and so is a comment - {# ... #} and {-- ... --}, found as padCommentStrip
  // finds them: a tag inside one never runs, and an {extends}, a {block} or a {meta} that
  // was commented out was applied by the assembly all the same.

  function padLayoutMask ( $text, &$masks ) {

    $text = preg_replace_callback ( '/\{ignore\}.*?\{\/ignore\}/s', function ( $match ) use ( &$masks ) {
      $masks [] = $match [0];
      return "\u{E0F2}" . ( count ( $masks ) - 1 ) . "\u{E0F3}";
    }, (string) $text );

    foreach ( [ '{--', '{#' ] as $open )
      for ( $pos = strpos ( $text, $open ); $pos !== FALSE; $pos = strpos ( $text, $open, $pos ) ) {
        $end = padLayoutComment ( $text, $pos, $open );
        if ( $end === FALSE ) {
          $pos += strlen ( $open );
          continue;
        }
        $masks [] = substr ( $text, $pos, $end - $pos );
        $mark     = "\u{E0F2}" . ( count ( $masks ) - 1 ) . "\u{E0F3}";
        $text     = substr ( $text, 0, $pos ) . $mark . substr ( $text, $end );
        $pos     += strlen ( $mark );
      }

    return $text;

  }

  // A wrapper's text with every @page@ inside a comment made @page_, which marks nothing:
  // the comment never renders, and the text keeps its length, so the source map still
  // places the wrapper's lines (build/base.php).

  function padLayoutNoted ( $text ) {

    $text = (string) $text;

    if ( ! str_contains ( $text, '@page@' ) or ( ! str_contains ( $text, '{#' ) and ! str_contains ( $text, '{--' ) ) )
      return $text;

    $masks  = [];
    $masked = padLayoutMask ( $text, $masks );

    foreach ( $masks as $index => $mask )
      if ( preg_match ( '/^\{(#|--)/', $mask ) )
        $masks [$index] = str_replace ( '@page@', '@page_', $mask );

    return padLayoutUnmask ( $masked, $masks );

  }

  // The end of the comment that opens at $pos, or FALSE when none opens there: {-- needs
  // whitespace after it, {#name} and {#name | pipe} are the option sigil, and a {# that
  // meets another {# before any #} is no comment - the rules of padCommentStrip.

  function padLayoutComment ( $text, $pos, $open ) {

    if ( $open == '{--' ) {
      $after = $text [$pos + 3] ?? '';
      $close = strpos ( $text, '--}', $pos + 3 );
      return ( ( $after !== '' and ! ctype_space ( $after ) ) or $close === FALSE ) ? FALSE : $close + 3;
    }

    if ( preg_match ( '/\G\{#[A-Za-z_][A-Za-z0-9_]*\s*[}|]/', $text, $match, 0, $pos ) )
      return FALSE;

    $close = strpos ( $text, '#}', $pos + 2 );
    $next  = strpos ( $text, '{#', $pos + 2 );

    return ( $close === FALSE or ( $next !== FALSE and $next < $close ) ) ? FALSE : $close + 2;

  }

  // A comment can hold an {ignore} that was set aside first, so what comes back can hold a
  // mark of its own.

  function padLayoutUnmask ( $text, $masks ) {

    for ( $i = 0; $i < 3 and str_contains ( $text, "\u{E0F2}" ); $i++ )
      $text = preg_replace_callback ( '/\x{E0F2}(\d+)\x{E0F3}/u', fn ( $match ) => $masks [ (int) $match [1] ] ?? '', $text );

    return $text;

  }

?>
