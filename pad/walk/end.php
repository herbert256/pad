<?php

  if ( $GLOBALS ['padInfo'] )
    include PAD . 'events/walk.php';

  $padWalk [$pad] = 'end';

  $padContent = $padResult [$pad];
  $padParm    = $padOpt [$pad] [1] ?? '';
  include PAD . "types/" . $padType [$pad] . ".php";
  
  $padResult [$pad] = $padContent;

  include PAD . "level/flags.php"; 
  
?>