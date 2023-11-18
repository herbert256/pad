<?php
   
  $padPage = $padRestart;

  if ( isset($padRestartVars) ) {

    foreach ( $padRestartVars as $padK => $padV )
      $GLOBALS [$padK] = $padV;

    $padRestartVars = [];

  }

  $padStartType = 'restart';

  if ( $padXref ) 
    padXref ( 'start', $padStartType );

  include pad . 'start/pad.php';
  
?>