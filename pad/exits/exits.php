<?php

  padTimingStart ('exit');

  $padOutput = $padResult [0];

  $padOutput = str_replace ( 
    [ '&open;', '&close;', '&pipe;', '&eq;', '&comma;'], 
    [ '{',      '}',       '|',      '=',     ','], 
    $padOutput 
  );

  if ( count ($padSanitize) )
    include 'sanitize.php';
 
  if ( $padTidy )
    include 'tidy.php';

  if ( $padRemoveWhitespace )
    include 'whitespace.php';
  
  $padEtag = padMd5 ($padOutput);

  if ( $padTrackFileData )
    padTrackFileData ();

  if ( $padTrackDbData )
    padTrackDbData ();

  $padStop = ( $padEtag304 and ($padCacheClient??'') == $padEtag ) ? 304 : 200;

  if ( $padCache and $padCacheServerAge )
    include PAD . 'cache/exits.php';

  padTimingEnd ('exit');

  padStop ($padStop);

?>