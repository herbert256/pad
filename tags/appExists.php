<?php

  if  ( ! padValid  ( $padPrm [$pad] [1] ) )                                 
    return FALSE;

  $padExits = APPS . $padPrm [$pad] [1];

  return is_dir ($padExits);

?>