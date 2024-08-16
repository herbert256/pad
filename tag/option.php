<?php

  global $padPrm;

  if ( isset ( $padPrm [$padIdx] [$parm] ) )
    return $padPrm [$padIdx] [$parm];
  else
    return NULL;

?>