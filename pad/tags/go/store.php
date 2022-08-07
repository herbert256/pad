<?php

  if ( $pad_walk == 'start' and ($pTag == 'data' or $pTag == 'flag' or $pPrmsType == 'close' ) ) {
    $pad_walk = 'end';
    return TRUE;
  }

  $pStore_name = 'pad_'.$pTag.'_store';

  if ( isset ( $pPrmsVal [$p] [0] ) or isset ( $pPrmsVal [$p] [1] ) ) 
    $pName [$p] = $pPrmsVal [$p] [0];

  if ( isset ( $pPrmsVal [$p] [1] ) )
    $pStore_source = $pPrmsVal [$p] [1];  
  else
    $pStore_source = $pContent;

  $pName [$p] = $pName [$p];

  if ( $pTag == 'content') {

    $pStore_data = pMake_content ($pStore_source);
  
  } elseif ( $pTag == 'data' ) {

    if ( ! pIs_default_data ( $pData[$p] ) )
      $pStore_data = $pData[$p];
    elseif ( $pStore_source )
      $pStore_data = pMake_data ($pStore_source, pTag_parm('type'), $pName [$p]);
    else
      $pStore_data = '';

  } elseif ( $pTag == 'flag' ) {

    if ( $pStore_source )
      $pStore_data = pMake_flag ($pStore_source);
    else
      $pStore_data = $pHit;

  }

  $GLOBALS [$pStore_name] [$pName [$p]] = $pStore_data;

  $pContent = '';

?>