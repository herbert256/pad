<?php
     
  $padWalk [$pad] = 'next';
  
  $padContent = $padBase [$pad];
  include PAD . "pad/level/go.php"; 
  $padBase [$pad] = $padContent;

  include PAD . "pad/level/flags.php";

  if ( $padWalk [$pad] ) {

    if ( $padArray )
      $padData [$pad] = $padTagResult;
    elseif ( $padText ) 
      $padBase [$pad] = $padTagResult;
    elseif ( $padElse ) 
      $padBase [$pad] = $padFalse [$pad];
 
    reset ( $padData [$pad] );

  }

?>