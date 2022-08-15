<?php

  $padWalk [$pad] = 'end';

  $padContent = $padResult [$pad];
  include PAD . "level/type_go.php"; 
  $padResult [$pad] = $padContent;

  include PAD . "level/flags.php";

  // if ( $padNull ) 
  //   $padResult [$pad] = '';
  // elseif ( $padArray )
  //   $padData [$pad] = $padTagResult;
  // elseif ( $padText ) 
  //   $padResult [$pad] = $padTagResult;
  // elseif ( $padElse ) 
  //   $padResult [$pad] = $padFalse [$pad];
 

?>