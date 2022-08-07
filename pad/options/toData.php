<?php

  $pStore_name = $pPrmsTag[$p] ['toData'];

  if ( !$pPair and !$pContent and !pIs_default_data($pData[$p]) ) {
    $pDataStore [$pStore_name] = $pData[$p];
    return;
  }

  if ( $pWalks [$pad ] <> 'start' ) 
    $pDataStore [$pStore_name] = $pWalks_data [ $pad];
  else
    $pDataStore [$pStore_name] = $pData[$p];

  $pResult[$p] = '';
  
?>