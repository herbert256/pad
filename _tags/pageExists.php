<?php

  if  ( ! padValid  ( $padOpt [$pad] [1] ) )                                 
    return FALSE;

  $padExits = padApp . $padOpt [$pad] [1];

  if ( padExists ("$padExits.html")       ) return TRUE;
  if ( padExists ("$padExits.php")        ) return TRUE;
  if ( padExists ("$padExits/index.html") ) return TRUE;
  if ( padExists ("$padExits/index.php")  ) return TRUE;

  return FALSE;

?>