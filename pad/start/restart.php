<?php
   
  $padPage = $padRestart;

  if ( isset($padRestartVars) ) {

    foreach ( $padRestartVars as $padK => $padV )
      $GLOBALS [$padK] = $padV;

    $padRestartVars = [];

  }

  $padStartType = 'restart';

  include pad . 'start/pad.php';
  
?>