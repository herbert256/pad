<?php

  foreach ( $GLOBALS as $padK => $padV ) {

    if ( padValidStore ($padK) )
      unset ( $GLOBALS [$padK] );

    if ( padSaveField ( $padK) and ! in_array($padK, $padPagePad [$pad]) ) 
      unset ( $GLOBALS [$padK] );
 
  }

  foreach ( $padPageApp [$pad] as $padK => $padV )
    $GLOBALS [$padK] = $padV;
  
  padRestore ( $padPagePad [$pad] ) ;

?>