<?php
   
  padEmptyBuffers ( $padIgnored );
   
  $padPage = $padRestart;

  if ( isset ( $padRestartVars ) ) {

    foreach ( $padRestartVars as $padK => $padV )
      $GLOBALS [$padK] = $padV;

    $padRestartVars = [];

  }

  include 'start/enter/start.php';
  
?>