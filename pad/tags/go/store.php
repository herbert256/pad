<?php

  if ( $padWalk [$pad] == 'start' and $padPrmType [$pad] == 'close' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padStoreName = 'pad' . ucwords($padTag[$pad]) . 'Store';

  if ( isset ( $padParm ) or isset ( $padOpt [$pad] [2] ) ) 
    $padName [$pad] = $padParm;

  if ( ! $padContent )
    $padStoreSource = $padOpt [$pad] [2];  
  elseif ($padTag [$pad] == 'content' and $padWalk [$pad] == 'start')
    $padStoreSource = $padBase [$pad];
  else
    $padStoreSource = $padContent;

  if ( $padTag [$pad] == 'content') {

    if ( $padWalk [$pad] == 'start' )
      $padStoreData = $padBase [$pad];
    else
      $padStoreData = padMakeContent ($padStoreSource);

  } elseif ( $padTag [$pad] == 'data' ) {

    if ( ! padIsDefaultData ( $padData [$pad] ) )
      $padStoreData = $padData [$pad];
    else
      $padStoreData = padData ($padStoreSource, padTagParm('type'), $padName [$pad]);

  } elseif ( $padTag [$pad] == 'flag' ) {

    $padStoreData = padMakeFlag ($padStoreSource);

  }

  $GLOBALS [$padStoreName] [$padName [$pad]] = $padStoreData;

  if ( $GLOBALS ['padInfo'] )
    include '/pad/info/events/store.php';

  $padContent = '';

  return NULL;

?>