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

  // The unit is chosen on the size as it will be written: 1048575 bytes rounded to no
  // decimals is 1024 KB, which is 1 MB - rounding after the choice wrote '1024 KB'.

  while ( $unit < count ( $units ) - 1
          and ( abs ( $size ) >= 1024 or abs ( round ( $size, $precision ) ) >= 1024 ) ) {
    $size /= 1024;
    $unit++;
  }

  $text = number_format ( round ( $size, $precision ), $precision, '.', '' );

  if ( str_contains ( $text, '.' ) )
    $text = rtrim ( rtrim ( $text, '0' ), '.' );

  return $text . ' ' . $units [$unit];

?>
