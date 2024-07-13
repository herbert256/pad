<?php

  if ( ! isset ($padPrm [$pad] ['url']) )
    $padPrm [$pad] ['url'] = $padParm;

  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padPrm [$pad] ['url'] = padAddGet ($padPrm [$pad] ['url'], $padK, $padV );

  $padPrm [$pad] ['url'] = str_replace('SELF://', "$padHost/", $padPrm [$pad] ['url']);

  $padCurl = padCurl ( $padPrm [$pad]);

  if ( $padCurl ['result'] <> '200' )
    padError ( "Curl failed: " . $padCurl ['result'] . ' ' . $padCurl ['url'] );

  return $padCurl ['data'];

?>