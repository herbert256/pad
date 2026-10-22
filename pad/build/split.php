<?php

  // Splits the built page on @else@ into $padBuildTrue (data found) and $padBuildFalse
  // (no data), which build/page.php picks between once it knows what the page returned.
  //
  // Only an @else@ with all tag pairs balanced in front of it counts, so an @else@ that
  // belongs to a nested tag is skipped and the search moves on; when none qualifies both
  // variables are left as they were.
  //
  // The page is still the text of its files here, its whitespace control not yet taken off
  // (occurrence/init.php): the marker is looked for in padElseText (lib/content.php), where
  // {~none~} opens a none pair as {none} does, and the page is cut at the same position.

  if ( ! str_contains ( $padBuildTrue, '@else@' ) )
    return;

  $padSplitText = padElseText ( $padBuildTrue );

  $padOpenClose = padOpenCloseList ( $padSplitText ) ;

  $padPos = strpos ( $padSplitText, '@else@');

  while ( $padPos !== FALSE) {

    if  ( padOpenCloseCount ( substr ( $padSplitText, 0, $padPos ), $padOpenClose) ) {
      if ( $padCheckSyntax )
        padElseSecond ( $padSplitText, $padPos, $padOpenClose );
      $padBuildFalse = substr ( $padBuildTrue, $padPos+6  );
      $padBuildTrue  = substr ( $padBuildTrue, 0, $padPos );
      if ( $padInfo )
        include PAD . 'events/else.php';
      return;
    }

    $padPos = strpos ( $padSplitText, '@else@', $padPos+1);

  }

?>
