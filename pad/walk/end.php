<?php

  $padWalk [$pad] = 'end';

  $padContent = $padResult [$pad];
  include PAD . "pad/level/go.php"; 
  $padResult [$pad] = $padContent;

  include PAD . "pad/level/flags.php"; 
  
?>