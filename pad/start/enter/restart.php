<?php
   
  padEmptyBuffers ();
   
  $padPage = $padRestart;

  if ( isset ( $padRestartVars ) ) {

    foreach ( $padRestartVars as $padK => $padV )
      $GLOBALS [$padK] = $padV;

    $padRestartVars = [];

  }

  include PAD . 'start/enter/start.php';
  
?>