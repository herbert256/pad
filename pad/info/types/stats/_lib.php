<?php


  function padInfoStatsStart () {

    $GLOBALS ['padInfoStatsStart'] = getrusage();

  }



  function padInfoStatsEnd () {

    $start = $GLOBALS ['padInfoStatsStart'];
    $end   = getrusage ();
    
    $user = ( $end   ['ru_utime.tv_sec'] * 1000000 + $end   ['ru_utime.tv_usec'] )
          - ( $start ['ru_utime.tv_sec'] * 1000000 + $start ['ru_utime.tv_usec'] );

    $system = ( $end   ['ru_stime.tv_sec'] * 1000000 + $end   ['ru_stime.tv_usec'] )
            - ( $start ['ru_stime.tv_sec'] * 1000000 + $start ['ru_stime.tv_usec'] );

    $GLOBALS ['padInfoStatsInfo'] =  [
      'user'     => $user,
      'system'   => $system,
      'duration' => padDuration ()
    ];

    include 'events/stats.php';

  }

  
?>