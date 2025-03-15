<?php
  
  $padOutput = padOutput ( $padResult [0] );

  if ( $padTidy or $padMyTidy )
    include 'exits/tidy.php';
 
  $padEtag = padMD5 ($padOutput);
  $padStop = 200;

  if ( $padCache and $padCacheServerAge )
    include 'cache/exits.php';

  include 'exits/output.php';

?>