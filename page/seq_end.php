<?php

  foreach ( $GLOBALS as $padK => $padV) 
    if ( substr($padK, 0, 6) == 'padSeq' )
      unset ( $GLOBALS [$padK] );
 
   foreach ( $padPageSeq [$pad] as $padK => $padV) 
      $GLOBALS [$padK] = $padPageSeq [$pad] [$padK];
 
?>