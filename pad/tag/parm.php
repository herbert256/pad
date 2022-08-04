<?php

  global $pad_parms;

  if ( isset ( $pad_parms [$pad_idx] ['parms_key'] [$parm] ) )
    return $pad_parms [$pad_idx] ['parms_key'] [$parm];
  else
    return NULL;

?>