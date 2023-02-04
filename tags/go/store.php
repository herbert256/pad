<?php

  if ( $padWalk [$pad] == 'start' and ($padTag [$pad] == 'data' or $padTag [$pad] == 'flag' or $padPrmType [$pad] == 'close' ) ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padStoreName = 'pad' . ucwords($padTag[$pad]) . 'Store';

  if ( isset ( $padPrm [$pad] [1] ) or isset ( $padPrm [$pad] [2] ) ) 
    $padName [$pad] = $padPrm [$pad] [1];

  if ( ! $padContent )
    $padStoreSource = $padPrm [$pad] [2];  
  else
    $padStoreSource = $padContent;

  if ( $padTag [$pad] == 'content') {

    $padStoreData = padMakeContent ($padStoreSource);
  
  } elseif ( $padTag [$pad] == 'data' ) {

    if ( ! padIsDefaultData ( $padData [$pad] ) )
      $padStoreData = $padData [$pad];
    else
      $padStoreData = padMakeData ($padStoreSource, padTagParm('type'), $padName [$pad]);

  } elseif ( $padTag [$pad] == 'flag' ) {

    $padStoreData = padMakeFlag ($padStoreSource);

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