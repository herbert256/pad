<?php

  if ( pTag_parm ('content') ) $padTrue  [$pad] = include PAD . "options/content.php";    
  if ( pTag_parm ('else')    ) $padFalse [$pad] = include PAD . "options/else.php";    
  if ( pTag_parm ('data')    ) $padData  [$pad] = include PAD . "options/data.php";   
  
?>