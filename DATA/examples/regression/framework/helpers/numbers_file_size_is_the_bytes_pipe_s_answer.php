<?php

  // padNumberFileSize: units of 1024, the unit chosen on the size as it will be written,
  // at most precision decimals - 0 unless given, where the bytes pipe has 2.

  $r = json_encode ( [
    padNumberFileSize ( 1536 ),
    padNumberFileSize ( 1536, 1 ),
    padNumberFileSize ( 1536, 2 ),
    padNumberFileSize ( 1048575 ),
    padNumberFileSize ( 1023.996, 1 ),
    padNumberFileSize ( 3221225472 ),
    padNumberFileSize ( 123456789, 1 ),
    padNumberFileSize ( 500 ),
    padNumberFileSize ( 0 ),
    padNumberFileSize ( '2048' ),
    padNumberFileSize ( NULL )
  ] );

  $size = 123456789;

?>
