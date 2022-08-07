<?php

  pTiming_start ('exit');

  $pOutput = $pResult [1];

  $pOutput = str_replace ( 
    [ '&open;', '&close;', '&pipe;', '&eq;', '&comma;'], 
    [ '{',      '}',       '|',      '=',     ','], 
    $pOutput 
  );

  if ( count ($pSanitize) )
    include PAD . 'exits/sanitize.php';
 
  if ( $pTidy )
    include PAD . 'exits/tidy.php';

  if ( $pRemove_whitespace )
   include 'whitespace.php';
  
  $pEtag = pMd5 ($pOutput);

  if ( $pTrack_file_data )
    pTrack_file_data ();

  if ( $pTrack_db_data )
    pTrack_db_data ();

  $pStop = ( $pEtag_304 and ($pCache_client??'') == $pEtag ) ? 304 : 200;

  if ( $pCache and $pCache_server_age )
    include PAD . 'cache/exits.php';

  pTiming_end ('exit');

  pStop ($pStop);

?>