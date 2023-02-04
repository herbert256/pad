<?php

  if ( padTagParm ('content') ) $padTrue  [$pad] = include PAD . "options/content.php";    
  if ( padTagParm ('else')    ) $padFalse [$pad] = include PAD . "options/else.php";    
  if ( padTagParm ('data')    ) $padData  [$pad] = include PAD . "options/data.php";   
  
?>