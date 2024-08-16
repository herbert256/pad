<?php

  if ( $GLOBALS ['padInfo'] )
    include '/pad/info/events/walk.php';

  $padWalk [$pad] = 'end';

  $padContent = $padResult [$pad];
  $padParm    = $padOpt [$pad] [1] ?? '';
  include "/pad/types/" . $padType [$pad] . ".php";
  
  $padResult [$pad] = $padContent;

  include "/pad/level/flags.php"; 
  
?>