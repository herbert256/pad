<?php

  // Pipe function bytes(precision): a byte count for people - 1536 is '1.5 KB', 3221225472
  // is '3 GB'. Units of 1024, at most precision decimals (2 unless given, a negative one
  // read as 0), trailing zeros dropped. The work is padNumberFileSize's (lib/number.php), so
  // the pipe and the PHP helper give one answer. A value that is not a number is returned as
  // it is.

  if ( ! is_numeric ( $value ) )
    return $value;

  return padNumberFileSize ( $value, max ( 0, (int) ( $parm [0] ?? 2 ) ) );

?>
