<?php

  // Shared tail for actions that are simply a PHP array function: calls $pqFunction with
  // $pqResult as the first argument followed by the items of $pqActionList, each swapped
  // for the stored sequence of that name when one exists. The call's return value is
  // returned to the include, so handlers write $pqResult = include ... - see
  // actions/types/intersection.php and actions/types/slice.php.

  $pqFunctionParms    = [];
  $pqFunctionParms [] = $pqResult;

  foreach ( $pqActionList as $pqFunctionParm )
    if ( isset ( $pqStore [$pqFunctionParm] ) )
      $pqFunctionParms [] = $pqStore [$pqFunctionParm];
    else
      $pqFunctionParms [] = $pqFunctionParm;

  return call_user_func_array ( $pqFunction, $pqFunctionParms );

?>