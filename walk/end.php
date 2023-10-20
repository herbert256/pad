<?php

  if ( $padOpt [$pad] [0] and substr_count( $padOpt [$pad] [0], '{' ) and substr_count( $padOpt [$pad] [0], '}' ) ) {

    $padPad = $padOpt [$pad] [0];
    include pad . "pad/entry/pad.php";
    $padOpt [$pad] [0] = $padPad;
    include pad . "level/parms.php";
    
  }

  $padWalk [$pad] = 'end';

  $padContent = $padResult [$pad];

  include pad . "types/" . $padType [$pad] . ".php";
  
  $padResult [$pad] = $padContent;

  include pad . "level/flags.php"; 
  
?>