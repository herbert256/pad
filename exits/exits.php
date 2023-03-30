<?php

  padTimingStart ('exit');

  $padOutput = $padResult [0];

  $padOutput = padUnescape ( $padOutput );

  if ( count ($padSanitize) )
    include 'sanitize.php';
 
  if ( $padTidy )
    include 'tidy.php';

  if ( $padRemoveWhitespace )
    include 'whitespace.php';
  
  $padEtag = padMD5 ($padOutput);

  if ( $padTrackFileData )
    padTrackFileData ( $padEtag, $padOutput );

  if ( $padTrackDbData )
    padTrackDbData ( $padEtag, $padOutput );

  $padStop = ( $padEtag304 and ($padCacheClient??'') == $padEtag ) ? 304 : 200;

  if ( $padCache and $padCacheServerAge )
    include pad . 'cache/exits.php';

  if ($padLog) 
    include pad . 'log/stop.php';

  padTimingEnd ('exit');

  padStop ($padStop);

?>