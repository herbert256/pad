<?php

  $padOccur [$pad]++;
  $padHtml  [$pad] = $padBase [$pad];
  $padKey   [$pad] = key($padData [$pad]);

  $padCurrent [$pad] = $padData [$pad] [$padKey [$pad]];

  $padInOccur = TRUE;
  
  include 'trace/start.php';

  if ( $padWalk [$pad] <> 'start' )
    $padWalkData [$pad] [] = $padCurrent [$pad];

  if ( padIsDefaultData ($padData [$pad]) ) {
    if ( isset($padPrm [$pad][1]) )
      padSetGlobal ( $padName [$pad], $padPrm [$pad][1] );
  } else
    padSetGlobal ( $padName [$pad], $padCurrent [$pad] );

  foreach ( $padCurrent [$pad] as $padK => $padV )
    padSetGlobal ( $padK, $padV );

  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include PAD . 'pad/callback/row.php' ;

?>