<?php

  if ( isset ( $padPrm [$pad] ['both'] ) ) {
    $pqResult = pqTruncate  ( $pqResult, 'left',  $padPrm [$pad] ['both'] );
    $pqResult = pqTruncate  ( $pqResult, 'right', $padPrm [$pad] ['both'] );
  }

  if ( isset ( $padPrm [$pad] ['left'] ) )
    $pqResult = pqTruncate  ( $pqResult, 'left', $padPrm [$pad] ['left'] );

  if ( isset ( $padPrm [$pad] ['right'] ) ) 
    $pqResult = pqTruncate  ( $pqResult, 'right', $padPrm [$pad] ['right'] );

  return $pqResult;
  
?>