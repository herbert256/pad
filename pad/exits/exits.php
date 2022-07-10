<?php

  $pad_output = $pad_result [1];

  $pad_output = str_replace ( 
    [ '&open;', '&close;', '&pipe;', '&eq;', '&comma;'], 
    [ '{',      '}',       '|',      '=',     ','], 
    $pad_output 
  );

  $pad_output = str_replace ( 
    [ '#open#', '#close#', '#pipe#', '#eq#', '#comma#'], 
    [ '{',      '}',       '|',      '=',     ','], 
    $pad_output 
  );

  if ( count ($pad_sanitize) )
    include PAD . 'exits/sanitize.php';
 
  if ( $pad_tidy )
    include PAD . 'exits/tidy.php';

  if ( count ($pad_close_tags) )
    include PAD . 'walk/close.php';

  if ( $pad_remove_whitespace )
   include 'whitespace.php';
  
  $pad_etag = pad_md5 ($pad_output);

  if ( $pad_track_file_data )
    pad_track_file_data ();

  if ( $pad_track_db_data )
    pad_track_db_data ();

  $pad_stop = ( $pad_etag_304 and ($pad_cache_client??'') == $pad_etag ) ? 304 : 200;

  if ( $pad_cache and $pad_cache_server_age )
    include PAD . 'cache/exits.php';

  pad_stop ($pad_stop);

?>