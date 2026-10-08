<?php

  // Thirty trading days of a share: a slow climb, a sharp fall on bad news halfway, and a
  // recovery - the prices from a fixed little random walk, so every render is the same.
  // A day of big moves is a day of big volume.

  $share = [];
  $seed  = 20260824;
  $close = 48.20;
  $day   = strtotime ( '2026-08-24 12:00 UTC' );
  $drift = [ 0.25, 0.3, 0.2, 0.35, 0.15, 0.3, 0.1, 0.2, -0.1, 0.05, 0.1, 0.15, -0.2, -4.2, -1.1,
             -0.4, 0.3, -0.2, 0.4, 0.5, 0.2, 0.45, 0.3, 0.6, 0.1, 0.35, 0.4, -0.2, 0.3, 0.5 ];

  $random = function () use ( &$seed ) {
    $seed = ( $seed * 1103515245 + 12345 ) % 2147483648;
    return $seed / 2147483648;
  };

  foreach ( $drift as $move ) {

    while ( gmdate ( 'N', $day ) > 5 )
      $day += 86400;

    $open   = $close + ( $random () - 0.5 ) * 0.6;
    $close  = $open + $move + ( $random () - 0.5 ) * 1.2;
    $high   = max ( $open, $close ) + $random () * 0.7;
    $low    = min ( $open, $close ) - $random () * 0.7;

    $share [] = [ 'day'    => gmdate ( 'M j', $day ),
                  'open'   => round ( $open,  2 ),
                  'high'   => round ( $high,  2 ),
                  'low'    => round ( $low,   2 ),
                  'close'  => round ( $close, 2 ),
                  'volume' => (int) round ( 120000 + 90000 * abs ( $close - $open ) + 40000 * $random () ) ];

    $day += 86400;

  }

?>
