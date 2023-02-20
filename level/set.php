<?php

  foreach ( $padPrmTmp as $padK => $padV ) { 

    list ($padSetName, $padSetValue ) = padSplit ('=', $padV);

    if ( substr($padSetName, 0, 1) == '$' ) {

      $padSetName = substr($padSetName, 1);

      if ( padValidVar ($padSetName) and $padSetValue !== '' ) {

        $padSet [$pad] [$padSetName] = padVarOpts ( '', padExplode($padSetValue, '|') );

        unset ( $padPrmTmp [$padK] ) ;
    
      }
  
    } 

  }

?>