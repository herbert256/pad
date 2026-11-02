<?php
  // Code 11 widths/checksum procedure checked against libzint (BSD-3-Clause).
  // Attribution and source revision: docs/subsystems/codes/code11.md and CODE11-LICENSE.
  function padCode11 ( $text, $checks = 2 ) {
    if ( ! is_string ( $text ) or ! preg_match ( '/^[0-9-]{1,140}$/D', $text ) )
      return 'Code 11 requires 1 to 140 digits or hyphens';
    if ( ! is_int ( $checks ) or $checks < 0 or $checks > 2 )
      return 'Code 11 checks must be 0 (none), 1 (C), or 2 (C and K)';
    $alphabet = '0123456789-';
    $patterns = ['111121','211121','121121','221111','112121','212111','122111','111221','211211','211111','112111'];
    $encoded = $text;
    foreach ( array_slice ( [10,9], 0, $checks ) as $cycle ) {
      $sum = 0; $weight = 1;
      for ( $i = strlen ( $encoded ) - 1; $i >= 0; $i-- ) {
        $sum += strpos ( $alphabet, $encoded [$i] ) * $weight;
        $weight = $weight % $cycle + 1;
      }
      $encoded .= $alphabet [ $sum % 11 ];
    }
    $widths = '112211';
    foreach ( str_split ( $encoded ) as $char ) $widths .= $patterns [ strpos ( $alphabet, $char ) ];
    $widths .= '11221';
    $bars = '';
    foreach ( str_split ( $widths ) as $i => $width ) $bars .= str_repeat ( $i % 2 ? '0' : '1', (int) $width );
    return [ $bars, [ [ strlen ( $bars ) / 2, $encoded ] ], [], 10, 0 ];
  }
?>
