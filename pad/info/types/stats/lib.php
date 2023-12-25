<?php


  function padStatsStart () {

    $GLOBALS ['padStatsCpuStart'] = getrusage();

  }



  function padStatsEnd () {

    $CpuStart = $GLOBALS ['padStatsCpuStart'];
    $CpuEnd   = getrusage ();
    
    $GLOBALS ['padStatsUser'] = 
       ( $CpuEnd   ['ru_utime.tv_sec'] * 1000000 + $CpuEnd   ['ru_utime.tv_usec'] )
    -  ( $CpuStart ['ru_utime.tv_sec'] * 1000000 + $CpuStart ['ru_utime.tv_usec'] );

    $GLOBALS ['padStatsSystem'] = 
       ( $CpuEnd   ['ru_stime.tv_sec'] * 1000000 + $CpuEnd   ['ru_stime.tv_usec'] )
    -  ( $CpuStart ['ru_stime.tv_sec'] * 1000000 + $CpuStart ['ru_stime.tv_usec'] );

    if ( padTrace and function_exists ( 'padTrace' ) )
      include pad . 'info/events/stats.php';

  }

  
?>