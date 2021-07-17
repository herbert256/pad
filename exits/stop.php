<?php

  if ( $GLOBALS['pad_stop'] == '000' )
    pad_dump ("Internal error, pad_stop not set");

  $pad_stop = $GLOBALS['pad_stop'];
  $pad_len  = ( $pad_stop == 200 ) ? strlen($GLOBALS['pad_output']) : 0;

  if ( $GLOBALS['pad_track_db_session'] )
    pad_track_db_session ($pad_stop);

  if ( $GLOBALS['pad_track_file'] )
    pad_track_file ();

  if ( $GLOBALS['pad_track_vars'] )
    pad_track_vars ("vars/" . $GLOBALS['app'] . "/" . $GLOBALS['page'] . "/" . $GLOBALS['PADREQID'] . ".html");

  pad_close_session ();
  pad_empty_buffers ();

  if ( ! isset($GLOBALS['pad_sent']) ) {

    $GLOBALS['pad_sent'] = TRUE;

    include 'headers.php';
  
    if ( $pad_stop == 200 )
      echo $GLOBALS['pad_output'];

  }  

  if ( $GLOBALS['pad_trace'] )
    include 'trace.php';

  pad_exit ();

?>