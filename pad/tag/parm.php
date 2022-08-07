<?php

  global $pPrmsTag;

  if ( isset ( $pPrmsTag [$pIdx] [$parm] ) )
    return $pPrmsTag [$pIdx] [$parm];
  else
    return NULL;

?>