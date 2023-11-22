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
  elseif ($padTag [$pad] == 'content' and $padWalk [$pad] == 'start')
    $padStoreSource = $padTrue [$pad];
  else
    $padStoreSource = $padContent;

  if ( $padTag [$pad] == 'content') {

    if ( $padWalk [$pad] == 'start' )
      $padStoreData = $padPadStart [$pad];
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

  if ( $padTraceActive )
    include pad . 'tail/types/trace/items/store.php';

  $padContent = '';

  return NULL;

?>