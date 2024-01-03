<?php

  foreach ( $padPrm [$pad] as $padK => $padV )
    padXref ( 'options', $padK );

  if ( $padType [$pad] == 'pad' ) {

    foreach ( $padPrm [$pad] as $padK => $padV )
      padXref ( 'tags-options', $padTag[$pad], $padK );

    foreach ( $padPrm [$pad] as $padK => $padV )
      padXref ( 'options-tags', $padK, $padTag[$pad] );

  }
  
?>