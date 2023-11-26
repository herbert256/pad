<?php

  $padStatsCpuEnd  = getrusage ();
  $padStatsTimeEnd = microtime (true) * 1000;
  
  $padStats ['Elapsed'] = intval ( $padStatsTimeEnd - $padStatsTimeStart );

  $padStats ['UserTime'] = 
     ( $padStatsCpuEnd   ['ru_utime.tv_sec'] * 1000 + intval ( $padStatsCpuEnd   ['ru_utime.tv_usec'] / 1000 ) )
  -  ( $padStatsCpuStart ['ru_utime.tv_sec'] * 1000 + intval ( $padStatsCpuStart ['ru_utime.tv_usec'] / 1000 ) );

  $padStats ['SystemTime'] = 
     ( $padStatsCpuEnd   ['ru_stime.tv_sec'] * 1000 + intval ( $padStatsCpuEnd   ['ru_stime.tv_usec'] / 1000 ) )
  -  ( $padStatsCpuStart ['ru_stime.tv_sec'] * 1000 + intval ( $padStatsCpuStart ['ru_stime.tv_usec'] / 1000 ) );

?>