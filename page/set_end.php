<?php

  foreach ( $padSaveVarsAssemble [$pad] as $padK => $padV) {
    if ( isset ( $GLOBALS [$padK] ) ) 
      unset ($GLOBALS [$padK] );
    $GLOBALS [$padK] = $padSaveVarsAssemble [$pad] [$padK];
  }

  foreach ( $padDeleteVarsAssemble [$pad] as $padK)
    if ( isset ( $GLOBALS [$padK] ) )
      unset ( $GLOBALS [$padK] );

  $padSaveVarsAssemble   [$pad] = [];
  $padDeleteVarsAssemble [$pad] = [];

?>