<?php

  padTimingStart ('exit');

  $padOutput = $padResult [0];

  $padOutput = str_replace ( 
    [ '&open;', '&close;', '&pipe;', '&eq;', '&comma;'], 
    [ '{',      '}',       '|',      '=',     ','], 
    $padOutput 
  );

  if ( count ($padSanitize) )
    include PAD . 'exits/sanitize.php';
 
  if ( $padTidy )
    include PAD . 'exits/tidy.php';

  if ( $padRemove_whitespace )
   include 'whitespace.php';
  
  $padEtag = padMd5 ($padOutput);

  if ( $padTrack_file_data )
    padTrackFileData ();

  if ( $padTrack_db_data )
    padTrackDbData ();

  $padStop = ( $padEtag_304 and ($padCache_client??'') == $padEtag ) ? 304 : 200;

  if ( $padCache and $padCache_server_age )
    include PAD . 'cache/exits.php';

  padTimingEnd ('exit');

  padStop ($padStop);

?>