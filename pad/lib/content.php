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

  function padContentElse ( $input, &$before, &$after ) {

    $pos  = strpos ( $input, '@else@' );
    $list = ( $pos === FALSE ) ? [] : padOpenCloseList ( $input ) ;

    while ( $pos !== FALSE) {

      if  ( padOpenCloseCount ( substr ( $input, 0, $pos ), $list ) ) {
        $before = substr ( $input, 0, $pos );
        $after  = substr ( $input, $pos+6  );
        return;
      }

      $pos = strpos ( $input, '@else@', $pos+1 );

    }

    $before = $input;
    $after  = '';

  }

  function padContentBeforeAfter ( $input, &$before, &$after ) {

    $pos = strpos ( $input, '@content@' );

    while ( $pos !== FALSE) {

      if  ( padOpenCloseCountOne ( substr ( $input, 0, $pos ), 'content' )
            and padOpenCloseCountOne ( substr ( $input, 0, $pos ), 'slot' ) ) {
        $before = substr ( $input, 0, $pos );
        $after  = substr ( $input, $pos+9  );
        return TRUE;
      }

      $pos = strpos ( $input, '@content@', $pos+1 );

    }

    return FALSE;

  }

?>
