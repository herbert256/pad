<?php
 
  foreach ( $GLOBALS ['padFunctionSave'] [$GLOBALS['pad']] as $padK => $padV ) {
    if ( isset ( $GLOBALS [$padK] ) ) 
      unset ( $GLOBALS [$padK] );
    $GLOBALS [$padK] = $padV;
  }

  $GLOBALS ['padFunctionSave'] [$GLOBALS['pad']] = [];
   
?>