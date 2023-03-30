<?php

  if ( $padWalk [$pad] == 'start' and $padPrmType [$pad] == 'close' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padStoreName = 'pad' . ucwords($padTag[$pad]) . 'Store';

  if ( isset ( $padOpt [$pad] [1] ) or isset ( $padOpt [$pad] [2] ) ) 
    $padName [$pad] = $padOpt [$pad] [1];

  if ( ! $padContent )
    $padStoreSource = $padOpt [$pad] [2];  
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

  if ( $padTrace ) include pad . 'trace/store.php';
  if ( $padLog )   include pad . 'log/store.php';

  $padContent = '';

  return NULL;

?>