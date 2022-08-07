<?php

  global $pParms;

  if ( isset ( $pParms [$pIdx] ['parms_key'] [$parm] ) )
    return $pParms [$pIdx] ['parms_key'] [$parm];
  else
    return NULL;

?>