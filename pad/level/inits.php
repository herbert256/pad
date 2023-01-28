<?php

  if ( padTagParm ('content') ) $padTrue  [$pad] = include PAD . "pad/options/content.php";    
  if ( padTagParm ('else')    ) $padFalse [$pad] = include PAD . "pad/options/else.php";    
  if ( padTagParm ('data')    ) $padData  [$pad] = include PAD . "pad/options/data.php";   
  
?>