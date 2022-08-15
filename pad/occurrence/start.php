<?php

  $padOccur [$pad]++;
  $padHtml  [$pad] = $padBase [$pad];
  $padKey   [$pad] = key($padData [$pad]);

  $padCurrent [$pad] = $padData [$pad] [$padKey [$pad]];

  include 'trace/start.php';

  if ( $padWalk [$pad] <> 'start' )
    $padWalkData  [$pad] [] = $padCurrent [$pad];

  if ( $pad ) {

    if ( pIs_default_data ($padData [$pad]) ) {
      if ( isset($padPrmsVal [$pad][0]) )
        pSet_global ( $padName [$pad], $padPrmsVal [$pad][0] );
    } else
      pSet_global ( $padName [$pad], $padCurrent [$pad] );

    foreach ( $padCurrent [$pad] as $padK => $padV )
      pSet_global ( $padK, $padV );

  }

  if ( isset($padPrmsTag [$pad] ['callback']) and ! isset($padPrmsTag [$pad] ['before']) )
    include PAD . 'callback/row.php' ;


?>