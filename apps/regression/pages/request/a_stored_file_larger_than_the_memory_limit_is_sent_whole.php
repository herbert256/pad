<?php

  // padUploadSend streams the stored file from the disk: sixteen megabytes come back whole
  // from a request that may take ten more than it uses (request/sent&which=big), where the
  // file was read into one string first, and one larger than memory_limit - a 200 MB
  // upload, a grown SQLite database - was a 500 "Allowed memory size exhausted".

  $bigGot    = padCurl ( $padGoExt . 'request/sent&which=big' );
  $bigData   = (string) ( $bigGot ['data'] ?? '' );
  $bigResult = substr ( $bigGot ['result'], 0, 3 ) . ' ' . strlen ( $bigData ) . ' bytes, '
             . ( $bigData === str_repeat ( 'PAD.', 4 << 20 ) ? 'whole' : 'not whole' );

  unset ( $bigGot, $bigData );

  @unlink ( DATA . 'regression-sent/big.bin' );

?>
