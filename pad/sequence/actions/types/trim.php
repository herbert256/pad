<?php

  // trim - cuts entries off the ends of the sequence. The amounts come from the both,
  // left and right tag options; a numeric parameter on trim itself supplies the count for
  // any of the three that were given without a value, and also fills in both whenever
  // both was left at zero. both is applied first, to each end, then any left and right
  // are taken off on top of that.

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