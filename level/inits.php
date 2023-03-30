<?php

  if ( padTagParm ('content') ) $padTrue  [$pad] = include pad . "options/content.php";    
  if ( padTagParm ('else')    ) $padFalse [$pad] = include pad . "options/else.php";    
  if ( padTagParm ('data')    ) $padData  [$pad] = include pad . "options/data.php";   
  
?>