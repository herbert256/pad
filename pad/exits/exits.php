<?php

  pTiming_start ('exit');

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
  
  $padEtag = pMd5 ($padOutput);

  if ( $padTrack_file_data )
    pTrack_file_data ();

  if ( $padTrack_db_data )
    pTrack_db_data ();

  $padStop = ( $padEtag_304 and ($padCache_client??'') == $padEtag ) ? 304 : 200;

  if ( $padCache and $padCache_server_age )
    include PAD . 'cache/exits.php';

  pTiming_end ('exit');

  pStop ($padStop);

?>