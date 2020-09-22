<?php

  global $pad_parameters;

  if ( isset ( $pad_parameters [$pad_idx] ['parms_key'] [$parm] ) )
    return $pad_parameters [$pad_idx] ['parms_key'] [$parm];
  else
    return NULL;

?>