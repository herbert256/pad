<?php
   
  padEmptyBuffers ();
   
  $padPage = $padRestart;

  if ( isset($padRestartVars) ) {

    foreach ( $padRestartVars as $padK => $padV )
      $GLOBALS [$padK] = $padV;

    $padRestartVars = [];

  }

  $padStartType = 'restart';

  if ( padXref ) 
    include pad . 'info/types/xref/items/entry.php';

  include pad . 'start/pad.php';
  
?>