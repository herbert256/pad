<?php

  $padOutput = $padResult [0];

  $padOutput = padUnescape ( $padOutput );

  include 'exits/output.php';
 
  $padEtag = padMD5 ($padOutput);

  if ( $padTrackFileData )
    padTrackFileData ( $padEtag, $padOutput );

  if ( $padTrackDbData )
    padTrackDbData ( $padEtag, $padOutput );

  $padStop = ( $padEtag304 and ($padCacheClient??'') == $padEtag ) ? 304 : 200;

  if ( $padCache and $padCacheServerAge )
    include 'cache/exits.php';

  padStop ($padStop);

?>