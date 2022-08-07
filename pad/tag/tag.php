<?php

  global $pParms;

  if ( isset ($pParms [$pIdx] [$parm] ) )
    return $pParms [$pIdx] [$parm];
  else
    return NULL;

?>