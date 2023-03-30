<?php

  if  ( ! padValid  ( $padOpt [$pad] [1] ) )                                 
    return FALSE;

  $padExits = padApps . $padOpt [$pad] [1];

  return is_dir ($padExits);

?>