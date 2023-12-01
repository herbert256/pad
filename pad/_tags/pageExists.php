<?php

  if  ( ! padValid  ( $padOpt [$pad] [1] ) )                                 
    return FALSE;

  $padExits = padApp . padPageGetName ($padOpt [$pad] [1] );

  if ( file_exists ("$padExits.pad")       ) return TRUE;
  if ( file_exists ("$padExits.php")        ) return TRUE;
  if ( file_exists ("$padExits/index.pad") ) return TRUE;
  if ( file_exists ("$padExits/index.php")  ) return TRUE;

  return FALSE;

?>