<?php

  $pStore_name = $pPrms_tag ['toData'];

  if ( !$pPair and !$pContent and !pIs_default_data($pData [$p]) ) {
    $pData_store [$pStore_name] = $pData [$p];
    return;
  }

  if ( $pad_walks [$pad ] <> 'start' ) 
    $pData_store [$pStore_name] = $pad_walks_data [ $pad];
  else
    $pData_store [$pStore_name] = $pData [$p];

  $pResult [$p] = '';
  
?>