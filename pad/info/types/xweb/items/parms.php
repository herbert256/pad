<?php

  foreach ( $padPrm [$pad] as $padK => $padV )
    padXweb ( 'options', $padK );

  if ( $padType [$pad] == 'pad' ) {

    foreach ( $padPrm [$pad] as $padK => $padV )
      padXweb ( 'tags-options', $padTag[$pad], $padK );

    foreach ( $padPrm [$pad] as $padK => $padV )
      padXweb ( 'options-tags', $padK, $padTag[$pad] );

  }
  
?>