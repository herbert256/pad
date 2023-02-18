<?php

  if ( $padPair [$pad] )
    return padError ("{set ...} can not be used as a open/close tag");

  foreach ( $padSet [$pad] as $padSetName => $padSetValue ) {

    $GLOBALS [$padSetName] = $padSetValue;
 
    for ( $padK=$pad; $padK; $padK-- ) {
      if ( isset ( $padSaveVars [$padK] [$padSetName] ) ) 
        unset ( $padK, $padSaveVars [$padK] [$padSetName] );
      if ( isset ( $padDeleteVars [$padK] [$padSetName] ) ) 
        unset ( $padK, $padDeleteVars [$padK] [$padSetName] );
    }
 
  }

  $padSet [$pad] = [];

  return TRUE;
  
?>