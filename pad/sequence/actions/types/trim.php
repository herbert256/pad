<?php

  $pqTrimBoth  = $padPrm [$pad] ['both']  ?? 0;
  $pqTrimLeft  = $padPrm [$pad] ['left']  ?? 0;
  $pqTrimRight = $padPrm [$pad] ['right'] ?? 0;

  if ( $pqActionParm and is_numeric ($pqActionParm) ) {
    if ( $pqTrimBoth  === TRUE ) $pqTrimBoth  = $pqActionParm;
    if ( $pqTrimLeft  === TRUE ) $pqTrimLeft  = $pqActionParm;
    if ( $pqTrimRight === TRUE ) $pqTrimRight = $pqActionParm;
    if ( $pqTrimBoth  === 0    ) $pqTrimBoth  = $pqActionParm;
  }

  if ( $pqTrimBoth ) {
    $pqResult = pqTruncate  ( $pqResult, 'left',  $pqTrimBoth );
    $pqResult = pqTruncate  ( $pqResult, 'right', $pqTrimBoth );
  }

  if ( $pqTrimLeft )
    $pqResult = pqTruncate  ( $pqResult, 'left', $pqTrimLeft );

  if ( $pqTrimRight )
    $pqResult = pqTruncate  ( $pqResult, 'right', $pqTrimRight );

?>
