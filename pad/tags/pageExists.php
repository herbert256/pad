<?php

  if  ( ! padValid  ( $padPrm [$pad] [0] ) )                                 
    return FALSE;

  $padExits = APP . 'pages/' . $padPrm [$pad] [0];

  if ( file_exists ("$padExits.html")       ) return TRUE;
  if ( file_exists ("$padExits.php")        ) return TRUE;
  if ( file_exists ("$padExits/index.html") ) return TRUE;
  if ( file_exists ("$padExits/index.php")  ) return TRUE;

  return FALSE;

?>