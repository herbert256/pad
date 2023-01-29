<?php

  if  ( ! padValid  ( $padPrm [$pad] ) )                                 
    return FALSE;

  $padExits = APP . 'pages/' . $padPrm [$pad];

  if ( file_exists ("$padExits.html")       ) return TRUE;
  if ( file_exists ("$padExits.php")        ) return TRUE;
  if ( file_exists ("$padExits/index.html") ) return TRUE;
  if ( file_exists ("$padExits/index.php")  ) return TRUE;

  return FALSE;

?>