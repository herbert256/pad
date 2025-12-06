<?php

  if ( ! isset ( $GLOBALS ['padInfoStatsStarted'] ) )
    return;
  
  global $padMicro, $padHR, $padApp, $padLog;

  $padStats_total = padDuration ();
  $padStats_boot  = padDuration ( 0, $padMicro );
  $padStats_user  = $padStats_total - $padStats_boot;
  $padStats_pad   = $padStats_user - $padApp;

  $GLOBALS ['padInfoStatsInfo'] =  [
    'total' => $padStats_total,
    'boot'  => $padStats_boot,
    'usr'   => $padStats_pad,
    'call'  => $padApp
  ]; 

  $GLOBALS ['padInfoStatsJson'] = json_encode ( $GLOBALS ['padInfoStatsInfo'] ) ;
  
  include PAD . 'events/stats.php';
 
?>