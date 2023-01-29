<?php

  if  ( ! padValid  ( $padPrm [$pad] ) )                                 
    return FALSE;

  $padExits = PAD . $padPrm [$pad];

  return is_dir ($padExits);

?>