<?php

  // Shared body of {data}, {content} and {bool}: puts the tag's content in a named store,
  // so that later on {thatName} addresses it (lib/type.php resolves names against the
  // stores).
  //
  // Parameters written on the closing tag only exist once the content has been walked, so
  // in that case the first visit just asks for a second one at level end ($padWalk =
  // 'end'). The name is the first parameter; the source is the tag's content, or the
  // second parameter when there is no content. {content} keeps the unprocessed source
  // when it is stored at the open tag and the rendered text otherwise, {data} runs the
  // source through padData() unless the level already carries data, {bool} reduces it to
  // TRUE/FALSE with padMakeFlag().
  //
  // The result lands in $padDataStore, $padContentStore or $padBoolStore; the content is
  // then cleared and NULL returned, so storing prints nothing.

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
      $padStoreData = $padSource [$pad];
    else
      $padStoreData = padMakeContent ($padStoreSource);

  } elseif ( $padTag [$pad] == 'data' ) {

    if ( ! padIsDefaultData ( $padData [$pad] ) )
      $padStoreData = $padData [$pad];
    else
      $padStoreData = padData ($padStoreSource, padTagParm('type'), $padName [$pad]);

  } elseif ( $padTag [$pad] == 'bool' ) {

    $padStoreData = padMakeFlag ($padStoreSource);

  }

  $GLOBALS [$padStoreName] [$padName [$pad]] = $padStoreData;

  if ( $padInfo )
    include PAD . 'events/store.php';

  $padContent = '';

  return NULL;

?>