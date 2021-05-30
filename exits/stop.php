<?php

  pad_trace ("end/start", "end=" . $GLOBALS['pad_stop']);

  $pad_stop = $GLOBALS['pad_stop'];
  $pad_len  = ( $pad_stop == 200 ) ? strlen($GLOBALS['pad_output']) : 0;

  if ( $GLOBALS['pad_track_db_session'] )
    pad_track_db_session ($pad_stop);

  if ( $GLOBALS['pad_track_file'] )
    pad_track_file ($pad_stop);

  if ( $GLOBALS['pad_track_vars'] )
    pad_track_vars ("vars/" . $GLOBALS['app'] . "/" . $GLOBALS['page'] . "/" . $GLOBALS['PADREQID'] . ".html");

  pad_close_session ();
  pad_empty_buffers ();

  if ( ! isset($GLOBALS['pad_sent']) ) {

    $GLOBALS['pad_sent'] = TRUE;

    include PAD_HOME . 'exits/headers.php';
  
    if ( $pad_stop == 200 )
      echo $GLOBALS['pad_output'];

  }  

  pad_trace ("pad/end", "app=" . $GLOBALS['app'] .  " page=" . $GLOBALS['page'] .  "  session=" . $GLOBALS['PADSESSID'] .  " request=" . $GLOBALS['PADREQID']);

  pad_exit ();

?>