<?php

  $padOutput = padUnescape ( $padResult [0] );

  if ( $padTidy or $padMyTidy )
    include PAD . 'exits/tidy.php';

  $padEtag = padMD5 ($padOutput);
  $padStop = 200;

  if ( $padCache and $padCacheServerAge )
    include PAD . 'cache/exits.php';

  include PAD . 'exits/output.php';

?>