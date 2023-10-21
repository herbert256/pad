<?php

  if  ( ! padValid  ( $padOpt [$pad] [1] ) )                                 
    return FALSE;

  $padExits = padApp . padPageGetName ($padOpt [$pad] [1] );

  if ( padExists ("$padExits.pad")       ) return TRUE;
  if ( padExists ("$padExits.php")        ) return TRUE;
  if ( padExists ("$padExits/index.pad") ) return TRUE;
  if ( padExists ("$padExits/index.php")  ) return TRUE;

  return FALSE;

?>