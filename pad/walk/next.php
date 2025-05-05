<?php
     
  if ( $padInfo )
    include 'events/walk.php';

  $padWalk [$pad] = 'next';
  
  include 'level/go.php'; 
  include 'level/flags.php';

  if ( $padWalk [$pad] ) {

    if ( $padArray [$pad] ) 
      $padData [$pad] = $padTagResult;
 
    reset ( $padData [$pad] );

  }

?>