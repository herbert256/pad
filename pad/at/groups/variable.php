<?php

  global $padSetLvl;

  if ( isset ( $padSetLvl [$padIdx] [$parm] ) )
    return $padSetLvl [$padIdx] [$parm];
  else
    return NULL;

?>