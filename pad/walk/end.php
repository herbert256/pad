<?php

  if ( $padInfo )
    include 'events/walk.php';

  $padWalk [$pad] = 'end';

  $padContent = $padResult [$pad];
  $padParm    = $padOpt [$pad] [1] ?? '';
  include "types/" . $padType [$pad] . ".php";
  
  $padResult [$pad] = $padContent;

  include "level/flags.php"; 
  
?>