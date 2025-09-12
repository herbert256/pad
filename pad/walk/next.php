<?php
     
  if ( $padInfo )
    include 'events/walk.php';

  $padWalk [$pad] = 'next';

  $padTry = 'level/go';
  include 'try/try.php';

  include 'level/flags.php';

  if ( $padWalk [$pad] ) {

    if ( $padArray [$pad] ) 
      $padData [$pad] = $padTagResult;
 
    reset ( $padData [$pad] );

  }

?>