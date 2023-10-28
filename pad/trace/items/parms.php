<?php

  if ( ! $padTraceTypes ['parse'] )
    return;

  foreach ( $padOpt [$pad] as $padK => $padV )
    if ( $padK and $padV )
      padTrace ( 'parm', 'opt',  "$padK ==> $padV" );

  foreach ( $padPrm [$pad] as $padK => $padV )
    padTrace ( 'parm', 'prm',  "$padK ==> $padV" );
  
  foreach ( $padSetLvl [$pad] as $padK => $padV )
    padTrace ( 'parm', 'lvl',  "$padK ==> $padV" );
  
  foreach ( $padSetOcc [$pad] as $padK => $padV )
    padTrace ( 'parm', 'occ',  "$padK ==> $padV" );

?>