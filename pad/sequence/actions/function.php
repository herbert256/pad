<?php

  // Shared tail for actions that are simply a PHP array function: calls $pqFunction with
  // $pqResult as the first argument followed by the items of $pqActionList, each swapped
  // for the stored sequence of that name when one exists. The call's return value is
  // returned to the include, so handlers write $pqResult = include ... - see
  // actions/types/intersection.php and actions/types/slice.php.
  //
  // An item that is neither a store nor a number is a name that was never pushed, or a
  // parameter that is not the count it should be: the sequence comes back untouched rather
  // than the name being handed to the array function, which took it as an array and ended
  // the request. An action written without a parameter at all is left alone the same way.

  if ( ! count ( $pqActionList ) )
    return $pqResult;

  $pqFunctionParms    = [];
  $pqFunctionParms [] = $pqResult;

  foreach ( $pqActionList as $pqFunctionParm )

    if      ( isset ( $pqStore [$pqFunctionParm] ) ) $pqFunctionParms [] = $pqStore [$pqFunctionParm];
    elseif  ( is_numeric ( $pqFunctionParm )       ) $pqFunctionParms [] = $pqFunctionParm + 0;
    else                                             return $pqResult;

  return call_user_func_array ( $pqFunction, $pqFunctionParms );

?>