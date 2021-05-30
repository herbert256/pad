<?php

  global $pad_parameters;

  if ( isset ($pad_parameters [$pad_idx] [$parm] ) )
    return $pad_parameters [$pad_idx] [$parm];
  else
    return NULL;

?>