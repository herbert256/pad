<?php


  function padInfoStatsStart () {

      return;

  }



  function padInfoStatsEnd () {

    if ( isset ( $GLOBALS ['padInfoStatsInfo'] ) )
      return;
    
    global $padMicro, $padHR, $padApp;

    $total = padDuration ();
    $php   = padDuration ( 0, $padMicro );
    $user  = $total - $php;
    $pad   = $user - $padApp;

    $GLOBALS ['padInfoStatsInfo'] =  [
      'tot'    => $total,
      'php'    => $php,
      'usr'    => $pad,
      'app'    => $padApp,
      'hr'     => padDurationHR () 
    ]; 

    $GLOBALS ['padInfoStatsJson'] = json_encode ( $GLOBALS ['padInfoStatsInfo'] ) ;

    include 'events/stats.php';

  }

  
?>