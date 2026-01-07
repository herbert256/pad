<?php

  global $padInfoStatsInfo, $padInfoStatsJson, $padInfoStatsStarted;

  if ( ! isset ( $padInfoStatsStarted ) )
    return;

  global $padMicro, $padHR, $padAppTime, $padLog;

  $padStats_total = padDuration ();
  $padStats_boot  = padDuration ( 0, $padMicro );
  $padStats_user  = $padStats_total - $padStats_boot;
  $padStats_pad   = $padStats_user - $padAppTime;

  $padInfoStatsInfo =  [
    'total' => $padStats_total,
    'boot'  => $padStats_boot,
    'usr'   => $padStats_pad,
    'call'  => $padAppTime
  ];

  $padInfoStatsJson = json_encode ( $padInfoStatsInfo ) ;

  include PAD . 'events/stats.php';

?>