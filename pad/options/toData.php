<?php

  $pStore_name = $pPrmsTag [$p] ['toData'];

  if ( !$pPair and !$pContent and !pIs_default_data($pData [$p]) ) {
    $pDataStore [$pStore_name] = $pData [$p];
    return;
  }

  if ( $pWalk  [$p] <> 'start' ) 
    $pDataStore [$pStore_name] = $pWalkData  [$p];
  else
    $pDataStore [$pStore_name] = $pData [$p];

  $pResult [$p] = '';
  
?>