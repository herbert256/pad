<?php

  global $pad_parms;

  if ( isset ($pad_parms [$pad_idx] [$parm] ) )
    return $pad_parms [$pad_idx] [$parm];
  else
    return NULL;

?>