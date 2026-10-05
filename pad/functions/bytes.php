<?php

  // Pipe function bytes(precision): a byte count for people - 1536 is '1.5 KB', 3221225472
  // is '3 GB'. Units of 1024, at most precision decimals (2 unless given), trailing zeros
  // dropped. A value that is not a number is returned as it is.

  if ( ! is_numeric ( $value ) )
    return $value;

  $precision = max ( 0, (int) ( $parm [0] ?? 2 ) );
  $size      = (float) $value;
  $units     = [ 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB' ];
  $unit      = 0;

  while ( abs ( $size ) >= 1024 and $unit < count ( $units ) - 1 ) {
    $size /= 1024;
    $unit++;
  }

  $text = number_format ( round ( $size, $precision ), $precision, '.', '' );

  if ( str_contains ( $text, '.' ) )
    $text = rtrim ( rtrim ( $text, '0' ), '.' );

  return $text . ' ' . $units [$unit];

?>
