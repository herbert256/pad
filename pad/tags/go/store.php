<?php

  if ( $pad_walk == 'start' and ($pTag == 'data' or $pTag == 'flag' or $pPrms_type == 'close' ) ) {
    $pad_walk = 'end';
    return TRUE;
  }

  $pStore_name = 'pad_'.$pTag.'_store';

  if ( isset ( $pPrms_val [0] ) or isset ( $pPrms_val [1] ) ) 
    $pName = $pPrms_val [0];

  if ( isset ( $pPrms_val [1] ) )
    $pStore_source = $pPrms_val [1];  
  else
    $pStore_source = $pContent;

  $pParms [$pad] ['name'] = $pName;

  if ( $pTag == 'content') {

    $pStore_data = pMake_content ($pStore_source);
  
  } elseif ( $pTag == 'data' ) {

    if ( ! pIs_default_data ( $pData[$pad] ) )
      $pStore_data = $pData[$pad];
    elseif ( $pStore_source )
      $pStore_data = pMake_data ($pStore_source, pTag_parm('type'), $pName);
    else
      $pStore_data = '';

  } elseif ( $pTag == 'flag' ) {

    if ( $pStore_source )
      $pStore_data = pMake_flag ($pStore_source);
    else
      $pStore_data = $pHit;

  }

  $GLOBALS [$pStore_name] [$pName] = $pStore_data;

  $pContent = '';

?>