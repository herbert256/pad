<?php

  // Works out the timings of the 'stats' info mode and publishes them.
  //
  // Splits the elapsed time into total, boot (up to $padMicro), usr (engine work) and call
  // ($padAppTime, time spent in application PHP), leaving them in $padInfoStatsInfo and, encoded,
  // in $padInfoStatsJson - which padWebStats (pad/lib/output.php) sends as the PAD-Stats header.
  //
  // Reached either from info/end/config.php or, earlier, from padWebStats itself when the
  // headers go out first; events/stats.php then feeds the same numbers into the trace.

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