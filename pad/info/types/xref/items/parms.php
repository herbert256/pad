<?php

  foreach ( $padPrm [$pad] as $padK => $padV )
    padXref ( 'parms', 'options', $padK );
  
  foreach ( $padSetLvl [$pad] as $padK => $padV )
    padXref ( 'parms', 'lvl', $padK );
  
  foreach ( $padSetOcc [$pad] as $padK => $padV )
    padXref ( 'parms', 'occ', $padK );

  foreach ( $padPrm [$pad] as $padK => $padV )
    padXref ( 'tags-options', $padTag[$pad], $padK );

  foreach ( $padPrm [$pad] as $padK => $padV )
    padXref ( 'options-tags', $padK, $padTag[$pad] );
  
?>