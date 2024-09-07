<?php

  $padSeqFunctionParms    = [];
  $padSeqFunctionParms [] = $padSeqResult;

  foreach ( $padSeqActionList as $padSeqFunctionParm )
    if ( isset ( $padSeqStore [$padSeqFunctionParm] ) )
      $padSeqFunctionParms [] = $padSeqStore [$padSeqFunctionParm];
    else
      $padSeqFunctionParms [] = $padSeqFunctionParm;
    
  return call_user_func_array ( $padSeqFunction, $padSeqFunctionParms );
  
?>