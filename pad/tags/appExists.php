<?php

  if  ( ! padValid  ( $padPrm [$pad] [0] ) )                                 
    return FALSE;

  $padExits = PAD . $padPrm [$pad] [0];

  return is_dir ($padExits);

?>