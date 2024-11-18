<?php
     
  if ( $GLOBALS ['padInfo'] )
    include PAD . 'events/walk.php';

  $padWalk [$pad] = 'next';
  
  include PAD . "catch/go.php"; 
  include PAD . "level/flags.php";

  if ( $padWalk [$pad] ) {

    if ( $padArray [$pad] ) 
      $padData [$pad] = $padTagResult;
 
    reset ( $padData [$pad] );

  }

?>