<?php

  if ( $padInfo )
    include PAD . 'events/walk.php';

  $padWalk [$pad] = 'next';

  $padTry = 'level/go';
  include PAD . 'try/try.php';

  include PAD . 'level/flags.php';

  if ( $padWalk [$pad] ) {

    if ( $padArray [$pad] )
      $padData [$pad] = $padTagResult;

    reset ( $padData [$pad] );

  }

?>
