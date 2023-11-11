<?php
   
  $padPage = $padRestart;

  if ( isset($padRestartVars) ) {

    foreach ( $padRestartVars as $padK => $padV )
      $GLOBALS [$padK] = $padV;

    $padRestartVars = [];

  }

  $padEntryType = 'restart';

  include pad . 'pad/entry/start.php';
  
?>