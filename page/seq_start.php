<?php

  $padPageSeq [$pad] = [];

  foreach ( $GLOBALS as $padK => $padV) 
    if ( substr($padK, 0, 6) == 'padSeq' ) {
      $padPageSeq [$pad] [$padK] = $GLOBALS [$padK];
      unset ( $GLOBALS [$padK] );
    }
 
?>