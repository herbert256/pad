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
    include PAD_HOME . 'exits/sanitize.php';

  if ( $pad_tidy )
    include PAD_HOME . 'exits/tidy.php';

  if ( count ($pad_close_tags) )
    include PAD_HOME . 'walk/close.php';
  
  $pad_etag = pad_md5 ($pad_output);

  if ( $pad_track_output )
    pad_track_output_file ();

  if ( $pad_track_db_data )
    pad_track_output_db ();

  $pad_stop = ( $pad_etag_304 and ($pad_cache_client??'') == $pad_etag ) ? 304 : 200;

  if ( $pad_cache and $pad_cache_server_age )
    include PAD_HOME . 'cache/exits.php';

  pad_stop ($pad_stop);

?>