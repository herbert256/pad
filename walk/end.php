<?php

  $padWalk [$pad] = 'end';

  $padContent = $padResult [$pad];

  include pad . "types/" . $padType [$pad] . ".php";
  
  $padResult [$pad] = $padContent;

  include pad . "level/flags.php"; 
  
?>