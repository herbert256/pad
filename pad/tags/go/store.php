<?php

  if ( $padWalk [$pad] == 'start' and ($padTag [$pad] == 'data' or $padTag [$pad] == 'flag' or $padPrmsType [$pad] == 'close' ) ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padStore_name = 'pad' . ucwords($padTag[$pad]) . 'Store';

  if ( isset ( $padPrmsVal [$pad] [0] ) or isset ( $padPrmsVal [$pad] [1] ) ) 
    $padName [$pad] = $padPrmsVal [$pad] [0];

  if ( isset ( $padPrmsVal [$pad] [1] ) )
    $padStore_source = $padPrmsVal [$pad] [1];  
  else
    $padStore_source = $padContent;

  $padName [$pad] = $padName [$pad];

  if ( $padTag [$pad] == 'content') {

    $padStore_data = padMakeContent ($padStore_source);
  
  } elseif ( $padTag [$pad] == 'data' ) {

    if ( ! padIsDefaultData ( $padData [$pad] ) )
      $padStore_data = $padData [$pad];
    elseif ( $padStore_source )
      $padStore_data = padMakeData ($padStore_source, padTagParm('type'), $padName [$pad]);
    else
      $padStore_data = '';

  } elseif ( $padTag [$pad] == 'flag' ) {

    if ( $padStore_source )
      $padStore_data = padMakeFlag ($padStore_source);
    else
      $padStore_data = FALSE;

  }

  $GLOBALS [$padStore_name] [$padName [$pad]] = $padStore_data;

  if ( $padTrace ) {
    $padTraceData = [
      'store'  => $padStore_name, 
      'entry'  => $padName [$pad],
      'source' => $padStore_source, 
      'result' => $padStore_data
    ];
    padFilePutContents ( $padLevelDir [$pad] . "/store.json", $padTraceData ); 
  }

  $padContent = '';

  return NULL;

?>