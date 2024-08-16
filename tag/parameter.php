<?php

  global $padOpt;

  if ( isset ( $padOpt [$padIdx] [$parm] ) )
    return $padOpt [$padIdx] [$parm];
  else
    return NULL;

?>