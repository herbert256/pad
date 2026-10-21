<?php

  // Merges what a tag produced back into the level's content, honouring the @content@ and
  // @else@ markers and the merge= parameter. Driven from level/go.php.
  //
  // padContentMerge       splits $new on @else@ and adds each half to the true or the
  //                       false content, depending on whether the tag hit
  // padContentSet         wraps one string around the other where @content@ appears, and
  //                       falls back to merge='top' (default), 'bottom' or 'replace'
  // padContentElse        finds the @else@ that sits outside any nested tag pair
  // padContentBeforeAfter the same search for @content@, returning the text either side
  //
  // Both searches use padOpenCloseCount from lib/template.php to skip markers that belong
  // to an inner tag, so nesting cannot be broken by a marker in a child. An @content@
  // inside a {slot} pair is that slot's frame for its fill (tags/slot.php), not the place
  // the tag's own content goes.

  function padContentMerge ( &$true, &$false, $new, $condition ) {

    padContentElse ( $new, $newTrue, $newFalse ) ;

    if ( $condition ) $true  = padContentSet ( $true,  $newTrue );
    else              $false = padContentSet ( $false, $newFalse );

  }

  function padContentSet ( $base, $new ) {

    $check = padContentBeforeAfter ( $new,  $before, $after );

    if ( $check )
      return $before . $base . $after;

    $check = padContentBeforeAfter ( $base,  $before, $after );

    if ( $check )
      return $before . $new . $after;

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

  function padMarkerText ( $text ) {

    if ( ! is_string ( $text ) )
      return $text;

    $text = padCommentStrip ( $text, TRUE );

    if ( ! str_contains ( $text, '~' ) )
      return $text;

    $text = preg_replace ( '/\{~(?=[\/A-Za-z_$!#&?^@])/', ' {', $text );
    $text = preg_replace ( '/(\{[\/A-Za-z_$!#&?^@][^{}]*?)~\}/', '$1} ', $text );

    return $text;

  }

  function padContentElse ( $input, &$before, &$after ) {

    $text = padMarkerText ( $input );
    $pos  = strpos ( $text, '@else@' );
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

    $text = padMarkerText ( $input );
    $pos  = strpos ( $text, '@content@' );

    while ( $pos !== FALSE) {

      if  ( padOpenCloseCountOne ( substr ( $text, 0, $pos ), 'content' )
            and padOpenCloseCountOne ( substr ( $text, 0, $pos ), 'slot' ) ) {
        $before = substr ( $input, 0, $pos );
        $after  = substr ( $input, $pos+9  );
        return TRUE;
      }

      $pos = strpos ( $text, '@content@', $pos+1 );

    }

    return FALSE;

  }

?>
