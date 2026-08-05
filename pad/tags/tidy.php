<?php

  // The {tidy} tag: reformats everything it wraps as tidied, indented HTML.
  //
  // Runs twice. On the way in it only returns TRUE and sets $padWalk [$pad] to 'end', which
  // tells level/end.php to call this handler again once the content has been rendered; on
  // that second call walk/end.php has put the finished output in $padContent and padTidy
  // rewrites it in place.

  if ( $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padContent = padTidy ( $padContent, TRUE );

  return TRUE;

?>