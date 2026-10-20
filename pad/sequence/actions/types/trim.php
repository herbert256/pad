<?php

  // trim - cuts entries off the ends of the sequence. The amounts come from the both,
  // left and right tag options; a numeric parameter on trim itself supplies the count for
  // any of the three that were given without a value, and also fills in both whenever
  // both was left at zero. both is applied first, to each end, then any left and right
  // are taken off on top of that.

  // Options of the tag's own run only: an action from an expression - sequence:trim([..]) in an
  // eval= - read the tag the expression stands in, right=1 of it took a value off the list.

  $pqTrimTag   = ( $pqEntry == 'tag' ) ? $padPrm [$pad] : [];

  $pqTrimBoth  = $pqTrimTag ['both']  ?? 0;
  $pqTrimLeft  = $pqTrimTag ['left']  ?? 0;
  $pqTrimRight = $pqTrimTag ['right'] ?? 0;

  // A count is a whole number: trim=1.5 and both=2.5 ended the request on PHP's deprecation
  // of a fractional offset, and right='x' on string * int, inside pqTruncate(). Strict mode
  // names it, and that count takes nothing off.

  foreach ( [ 'trim'  => 'pqActionParm', 'both'  => 'pqTrimBoth',
              'left'  => 'pqTrimLeft',   'right' => 'pqTrimRight' ] as $pqTrimName => $pqTrimVar ) {

    $pqTrimOne = $$pqTrimVar;

    if ( is_bool ( $pqTrimOne ) or ( is_scalar ( $pqTrimOne ) and (string) $pqTrimOne === '' ) )
      continue;

    if ( is_numeric ( $pqTrimOne ) and floor ( $pqTrimOne ) == $pqTrimOne )
      continue;

    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( "$pqTrimName= takes a count, not '" . ( is_scalar ( $pqTrimOne ) ? $pqTrimOne : gettype ( $pqTrimOne ) ) . "'" );

    $$pqTrimVar = 0;

  }

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
