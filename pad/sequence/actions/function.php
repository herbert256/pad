<?php

  $pqFunctionParms    = [];
  $pqFunctionParms [] = $pqResult;

  foreach ( $pqActionList as $pqFunctionParm )
    if ( isset ( $pqStore [$pqFunctionParm] ) )
      $pqFunctionParms [] = $pqStore [$pqFunctionParm];
    else
      $pqFunctionParms [] = $pqFunctionParm;

  return call_user_func_array ( $pqFunction, $pqFunctionParms );

?>
