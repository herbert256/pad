<?php

  global $padStrResApp;
  
  $padStrResApp [$padStrCnt] = [];

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( padValidStore ($padStrKey) ) {
      $padStrResApp [$padStrCnt] [$padStrKey] = $GLOBALS [$padStrKey];
      unset ( $GLOBALS [$padStrKey] );
    }
    
?>