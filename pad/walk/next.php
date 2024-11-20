<?php
     
  if ( $GLOBALS ['padInfo'] )
    include 'events/walk.php';

  $padWalk [$pad] = 'next';
  
  include "catch/go.php"; 
  include "level/flags.php";

  if ( $padWalk [$pad] ) {

    if ( $padArray [$pad] ) 
      $padData [$pad] = $padTagResult;
 
    reset ( $padData [$pad] );

  }

?>