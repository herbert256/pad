<?php

  if ( $padWalk [$pad] == 'start' and ($padTag [$pad] == 'data' or $padTag [$pad] == 'flag' or $padPrmsType [$pad] == 'close' ) ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padStoreName = 'pad' . ucwords($padTag[$pad]) . 'Store';

  if ( isset ( $padPrmsVal [$pad] [0] ) or isset ( $padPrmsVal [$pad] [1] ) ) 
    $padName [$pad] = $padPrmsVal [$pad] [0];

  if ( isset ( $padPrmsVal [$pad] [1] ) )
    $padStoreSource = $padPrmsVal [$pad] [1];  
  else
    $padStoreSource = $padContent;

  $padName [$pad] = $padName [$pad];

  if ( $padTag [$pad] == 'content') {

    $padStoreData = padMakeContent ($padStoreSource);
  
  } elseif ( $padTag [$pad] == 'data' ) {

    if ( ! padIsDefaultData ( $padData [$pad] ) )
      $padStoreData = $padData [$pad];
    elseif ( $padStoreSource )
      $padStoreData = padMakeData ($padStoreSource, padTagParm('type'), $padName [$pad]);
    else
      $padStoreData = '';

  } elseif ( $padTag [$pad] == 'flag' ) {

    if ( $padStoreSource )
      $padStoreData = padMakeFlag ($padStoreSource);
    else
      $padStoreData = FALSE;

  }

  $GLOBALS [$padStoreName] [$padName [$pad]] = $padStoreData;

  if ( $padTrace ) {
    $padTraceData = [
      'store'  => $padStoreName, 
      'entry'  => $padName [$pad],
      'source' => $padStoreSource, 
      'result' => $padStoreData
    ];
    padFilePutContents ( $padLevelDir [$pad] . "/store.json", $padTraceData ); 
  }

  $padContent = '';

  return NULL;

?>