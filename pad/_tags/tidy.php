<?php

  if ( $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padContent = padTidy ( $padContent, TRUE );

?>