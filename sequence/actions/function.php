<?php

  $padSeqFunctionParms    = [];
  $padSeqFunctionParms [] = $padSeqResult;

  foreach ( padExplode ( $padSeqActionValue, '|' ) as $store )
      if ( isset ( $padSeqStore [$store] ) )
        $padSeqFunctionParms [] = $padSeqStore [$store];
      else
        $padSeqFunctionParms [] = $store;

  return call_user_func_array ( $padSeqFunction, $padSeqFunctionParms );
  
?>