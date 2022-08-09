<?php

  if ( $pWalk [$p] == 'start' and ($pTag [$p] == 'data' or $pTag [$p] == 'flag' or $pPrmsType [$p] == 'close' ) ) {
    $pWalk [$p] = 'end';
    return TRUE;
  }

  $pStore_name = 'p' . ucwords($pTag[$p]) . 'Store';

  if ( isset ( $pPrmsVal [$p] [0] ) or isset ( $pPrmsVal [$p] [1] ) ) 
    $pName [$p] = $pPrmsVal [$p] [0];

  if ( isset ( $pPrmsVal [$p] [1] ) )
    $pStore_source = $pPrmsVal [$p] [1];  
  else
    $pStore_source = $pContent;

  $pName [$p] = $pName [$p];

  if ( $pTag [$p] == 'content') {

    $pStore_data = pMake_content ($pStore_source);
  
  } elseif ( $pTag [$p] == 'data' ) {

    if ( ! pIs_default_data ( $pData [$p] ) )
      $pStore_data = $pData [$p];
    elseif ( $pStore_source )
      $pStore_data = pMake_data ($pStore_source, pTag_parm('type'), $pName [$p]);
    else
      $pStore_data = '';

  } elseif ( $pTag [$p] == 'flag' ) {

    if ( $pStore_source )
      $pStore_data = pMake_flag ($pStore_source);
    else
      $pStore_data = FALSE;

  }

  $GLOBALS [$pStore_name] [$pName [$p]] = $pStore_data;

  if ( $pTrace ) {
    $pTraceData = [
      'store'  => $pStore_name, 
      'entry'  => $pName [$p],
      'source' => $pStore_source, 
      'result' => $pStore_data
    ];
    pFile_put_contents ( $pLevelDir [$p] . "/store.json", $pTraceData ); 
  }

  $pContent = '';

  return NULL;

?>