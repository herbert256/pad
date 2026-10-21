<?php

  // Merges what a tag produced back into the level's content, honouring the @content@ and
  // @else@ markers and the merge= parameter. Driven from level/go.php.
  //
  // padContentMerge       splits $new on @else@ and adds each half to the true or the
  //                       false content, depending on whether the tag hit
  // padContentSet         wraps one string around the other where @content@ appears, and
  //                       falls back to merge='top' (default), 'bottom' or 'replace'
  // padContentFill        puts the fill at every @content@ of the text's own level
  // padContentElse        finds the @else@ that sits outside any nested tag pair
  // padContentBeforeAfter the same search for @content@, returning the text either side
  //
  // Both searches use padOpenCloseCount from lib/template.php to skip markers that belong
  // to an inner tag, so nesting cannot be broken by a marker in a child. An @content@
  // inside a {slot} pair is that slot's frame for its fill (tags/slot.php), not the place
  // the tag's own content goes; padContentOwn says which pairs an @content@ belongs to.

  function padContentMerge ( &$true, &$false, $new, $condition ) {

    padContentElse ( $new, $newTrue, $newFalse ) ;

    if ( $condition ) $true  = padContentSet ( $true,  $newTrue );
    else              $false = padContentSet ( $false, $newFalse );

  }

  function padContentSet ( $base, $new ) {

    $set = padContentFill ( $new, $base );

    if ( $set !== FALSE )
      return $set;

    $set = padContentFill ( $base, $new );

    if ( $set !== FALSE )
      return $set;

    $merge = padTagParm ( 'merge', 'top' );

    if     ( $merge == 'bottom'  ) return $base . $new;
    elseif ( $merge == 'replace' ) return $new;

    // Any other word answered nothing at all, and the NULL ended the request in a PHP
    // deprecation far from the cause - merge='after'. Strict mode names it; the lenient walk
    // merges on top, the default.

    if ( $merge != 'top' and $GLOBALS ['padCheckSyntax'] )
      padError ( "there is no merge named '$merge' - it is top, bottom or replace" );

    return $new . $base;

  }

  // The text a construct marker is looked for in. A template read from its file - the page
  // itself, an application tag's .pad, an _include snippet - meets the marker searches before
  // the occurrence takes its whitespace control off (occurrence/init.php), and the balance
  // helpers know no ~: {~rows~} opened no rows pair there, so the @else@ inside it was taken
  // as the template's own and the text cut in the middle of that pair - the page ended at
  // [{~none~}x, a tag template lost the rest. This copy has the ~ of a tag's brace moved
  // just outside it, {~/rows~} reading " {/rows} ", so it is exactly as long as the template
  // and a position found in it is the same position in the template.
  //
  // Its comments are written over with spaces (padCommentStrip in lib/level.php): a comment
  // is gone before the template is scanned, and so is a marker the author wrote in one -
  // A{-- shows its @else@ part --}B cut the page inside the comment, and a tag template's
  // {-- the caller's content lands at @content@ --} took the caller's content into the
  // comment, which was then stripped with it.
  //
  // And a marker inside the braces of a tag is written over: it is a value of the tag - a
  // quoted parameter, {echo 'x@else@y'}, {if $s eq '@else@'} - never the content of a level.
  // The balance of the pairs did not see it, a single tag being no pair, and the level was
  // cut in the middle of the tag: "No close } found for open {".

  function padMarkerText ( $text ) {

    if ( ! is_string ( $text ) )
      return $text;

    $text = padCommentStrip ( $text, TRUE );

    if ( str_contains ( $text, '~' ) ) {
      $text = preg_replace ( '/\{~(?=[\/A-Za-z_$!#&?^@])/', ' {', $text );
      $text = preg_replace ( '/(\{[\/A-Za-z_$!#&?^@][^{}]*?)~\}/', '$1} ', $text );
    }

    return padMarkerInTags ( $text );

  }

  // A tag opens with a { that a name, a sigil or a slash follows, and ends at the } that
  // brings the brace depth back - a parameter may hold a tag of its own, {echo {$x}}. One
  // that never closes ends the search: what follows stays as it is.

  function padMarkerInTags ( $text ) {

    $at  = 0;
    $len = strlen ( $text );

    while ( ( $at = strpos ( $text, '{', $at ) ) !== FALSE ) {

      if ( ! preg_match ( '/\G\{[\/A-Za-z_$!#&?^@]/', $text, $match, 0, $at ) ) {
        $at++;
        continue;
      }

      $depth = 0;

      for ( $i = $at; $i < $len; $i += 1 + strcspn ( $text, '{}', $i + 1 ) )
        if ( $text [$i] == '{' )
          $depth++;
        elseif ( ! --$depth )
          break;

      if ( $i >= $len )
        break;

      $tag = substr ( $text, $at, $i - $at + 1 );

      if ( str_contains ( $tag, '@' ) )
        $text = substr_replace ( $text, preg_replace_callback ( '/@(else|content|start|end)@/',
                  fn ( $m ) => str_repeat ( ' ', strlen ( $m [0] ) ), $tag ), $at, strlen ( $tag ) );

      $at = $i + 1;

    }

    return $text;

  }

  // Every @content@ of the text's own level takes the fill - a template may show the content
  // in more than one place, <a>@content@</a><b>@content@</b>. Only the first was filled, and
  // the strict check then blamed the second as one that "nothing merges content into". FALSE
  // when the text has none.

  function padContentFill ( $text, $fill ) {

    if ( ! padContentBeforeAfter ( $text, $before, $after ) )
      return FALSE;

    $out = $before . $fill;

    while ( padContentBeforeAfter ( $after, $before, $rest ) ) {
      $out  .= $before . $fill;
      $after = $rest;
    }

    return $out . $after;

  }

  function padContentElse ( $input, &$before, &$after ) {

    $pos  = strpos ( $input, '@else@' );
    $text = ( $pos === FALSE ) ? '' : padMarkerText ( $input );
    $pos  = ( $pos === FALSE ) ? FALSE : strpos ( $text, '@else@' );
    $list = ( $pos === FALSE ) ? [] : padOpenCloseList ( $text ) ;

    while ( $pos !== FALSE) {

      if  ( padOpenCloseCount ( substr ( $text, 0, $pos ), $list ) ) {
        $before = substr ( $input, 0, $pos );
        $after  = substr ( $input, $pos+6  );
        return;
      }

      $pos = strpos ( $text, '@else@', $pos+1 );

    }

    $before = $input;
    $after  = '';

  }

  function padContentBeforeAfter ( $input, &$before, &$after ) {

    $pos  = strpos ( $input, '@content@' );
    $text = ( $pos === FALSE ) ? '' : padMarkerText ( $input );
    $pos  = ( $pos === FALSE ) ? FALSE : strpos ( $text, '@content@' );

    while ( $pos !== FALSE) {

      if  ( padContentOwn ( substr ( $text, 0, $pos ) ) ) {
        $before = substr ( $input, 0, $pos );
        $after  = substr ( $input, $pos+9  );
        return TRUE;
      }

      $pos = strpos ( $text, '@content@', $pos+1 );

    }

    return FALSE;

  }

  // Whether an @content@ after this text is the text's own: not inside an open {content} or
  // {slot} pair, whose frame it is, nor inside a {define} or {macro} written in the template -
  // that tag's own marker, filled where it is used - nor inside an {ignore} or {highlight}
  // sample, where it is text to show. Loops and conditions stay transparent: the content goes
  // in where the template puts it. A nested define's marker took the caller's content, and a
  // sample explaining @content@ printed the caller's template as its source.

  function padContentOwn ( $text ) {

    foreach ( [ 'content', 'slot', 'define', 'macro', 'ignore', 'highlight' ] as $tag )
      if ( ! padOpenCloseCountOne ( $text, $tag ) )
        return FALSE;

    return TRUE;

  }

?>
