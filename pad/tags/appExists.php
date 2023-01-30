<?php

  if  ( ! padValid  ( $padPrm [$pad] [1] ) )                                 
    return FALSE;

  $padExits = PAD . $padPrm [$pad] [1];

  return is_dir ($padExits);

?>