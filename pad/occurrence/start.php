<?php

  $padOccur [$pad]++;
  $padHtml  [$pad] = $padBase [$pad];
  $padKey   [$pad] = key($padData [$pad]);

  $padCurrent [$pad] = $padData [$pad] [$padKey [$pad]];

  include 'trace/start.php';

  if ( $padWalk [$pad] <> 'start' )
    $padWalkData  [$pad] [] = $padCurrent [$pad];

  if ( $pad ) {

    if ( padIsDefaultData ($padData [$pad]) ) {
      if ( isset($padPrmsVal [$pad][0]) )
        padSetGlobal ( $padName [$pad], $padPrmsVal [$pad][0] );
    } else
      padSetGlobal ( $padName [$pad], $padCurrent [$pad] );

    foreach ( $padCurrent [$pad] as $padK => $padV )
      padSetGlobal ( $padK, $padV );

  }

  if ( isset($padPrmsTag [$pad] ['callback']) and ! isset($padPrmsTag [$pad] ['before']) )
    include PAD . 'callback/row.php' ;


?>