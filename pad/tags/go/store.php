<?php

  if ( $pWalk[$p] == 'start' and ($pTag[$p]== 'data' or $pTag[$p]== 'flag' or $pPrmsType[$p] == 'close' ) ) {
    $pWalk[$p] = 'end';
    return TRUE;
  }

  $pStore_name = 'pad_'.$pTag.'_store';

  if ( isset ( $pPrmsVal[$p] [0] ) or isset ( $pPrmsVal[$p] [1] ) ) 
    $pName[$p] = $pPrmsVal[$p] [0];

  if ( isset ( $pPrmsVal[$p] [1] ) )
    $pStore_source = $pPrmsVal[$p] [1];  
  else
    $pStore_source = $pContent;

  $pName[$p] = $pName[$p];

  if ( $pTag[$p]== 'content') {

    $pStore_data[$p]= pMake_content ($pStore_source);
  
  } elseif ( $pTag[$p]== 'data' ) {

    if ( ! pIs_default_data ( $pData[$p] ) )
      $pStore_data[$p]= $pData[$p];
    elseif ( $pStore_source )
      $pStore_data[$p]= pMake_data ($pStore_source, pTag_parm('type'), $pName[$p]);
    else
      $pStore_data[$p]= '';

  } elseif ( $pTag[$p]== 'flag' ) {

    if ( $pStore_source )
      $pStore_data[$p]= pMake_flag ($pStore_source);
    else
      $pStore_data[$p]= $pHit;

  }

  $GLOBALS [$pStore_name] [$pName[$p]] = $pStore_data;

  $pContent = '';

?>