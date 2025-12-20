<?php

  global $padInfoStatsInfo, $padInfoStatsJson, $padInfoStatsStarted;

  if ( ! isset ( $padInfoStatsStarted ) )
    return;

  global $padMicro, $padHR, $padApp, $padLog;

  $padStats_total = padDuration ();
  $padStats_boot  = padDuration ( 0, $padMicro );
  $padStats_user  = $padStats_total - $padStats_boot;
  $padStats_pad   = $padStats_user - $padApp;

  $padInfoStatsInfo =  [
    'total' => $padStats_total,
    'boot'  => $padStats_boot,
    'usr'   => $padStats_pad,
    'call'  => $padApp
  ];

  $padInfoStatsJson = json_encode ( $padInfoStatsInfo ) ;

  include PAD . 'events/stats.php';

?>