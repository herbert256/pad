<?php

  foreach ( $GLOBALS as $padK => $padV ) {

    if ( padValidStore ($padK) )
      unset ( $GLOBALS [$padK] );

    if ( substr($padK, 0, 3) == 'pad' and ! in_array($padK, $padLevelVars) and ! in_array($padK, $padPagePad[$pad]) ) 
      if ( $padK <> 'padHistory' )
        unset ( $GLOBALS [$padK] );

  }

  foreach ( $padPageApp [$pad] as $padK => $padV ) {

    if ( isset ( $GLOBALS [$padK] ) ) 
      unset ( $GLOBALS [$padK] );

    $GLOBALS [$padK] = $padV;
  
  }

  foreach ( $padPagePad [$pad] as $padK => $padV ) {
  
    if ( isset ( $GLOBALS [$padK] ) ) 
      unset ( $GLOBALS [$padK] );
  
    $GLOBALS [$padK] = $padV;
  
  }

?>