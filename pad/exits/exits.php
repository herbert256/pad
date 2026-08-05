<?php

  // Turns the finished root-level result into the response.
  //
  // Unescapes it into $padOutput, tidies it when asked, derives the ETag the response
  // will carry, stores the page in the server cache when caching is on, and hands over to
  // exits/output.php, which emits it and ends the request. Reached from start/pad/go.php
  // as the last step of a normal request.

  $padOutput = padUnescape ( $padResult [0] );

  if ( $padTidy or $padMyTidy )
    include PAD . 'exits/tidy.php';

  $padEtag = padMD5 ($padOutput);
  $padStop = 200;

  if ( $padCache and $padCacheServerAge )
    include PAD . 'cache/exits.php';

  include PAD . 'exits/output.php';

?>