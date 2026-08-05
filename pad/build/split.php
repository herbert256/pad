<?php

  // Splits the built page on @else@ into $padBuildTrue (data found) and $padBuildFalse
  // (no data), which build/page.php picks between once it knows what the page returned.
  //
  // Only an @else@ with all tag pairs balanced in front of it counts, so an @else@ that
  // belongs to a nested tag is skipped and the search moves on; when none qualifies both
  // variables are left as they were.

  $padOpenClose = padOpenCloseList ( $padBuildTrue) ;

  $padPos = strpos ( $padBuildTrue, '@else@');

  while ( $padPos !== FALSE) {

    if  ( padOpenCloseCount ( substr ( $padBuildTrue, 0, $padPos ), $padOpenClose) ) {
      $padBuildFalse = substr ( $padBuildTrue, $padPos+6  );
      $padBuildTrue  = substr ( $padBuildTrue, 0, $padPos );
      if ( $padInfo )
        include PAD . 'events/else.php';
      return;
    }

    $padPos = strpos ( $padBuildTrue, '@else@', $padPos+1);

  }

?>