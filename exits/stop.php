<?php

  if ( $GLOBALS['pad_stop'] == '999' )
    pad_dump ("Internal error, pad_stop not set");

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

  if ( $GLOBALS['pad_trace'] ) {
    $pad_trace_dir_lvl = $GLOBALS['pad_trace_dir_lvl'] = $GLOBALS['pad_trace_dir_base'] . '/levels/0.pad';
    pad_trace ("pad/end", "stop=" . $pad_stop .  " len=" . $pad_len);
    pad_file_put_contents ($GLOBALS['pad_trace_dir_base'] . "/track.html",  pad_json ( pad_track ($GLOBALS['pad_stop']) ) );
    pad_file_put_contents ($GLOBALS['pad_trace_dir_base'] . "/output.html", $GLOBALS['pad_output']                        );
    pad_file_put_contents ($GLOBALS['pad_trace_dir_base'] . "/end.html",    pad_get_info ('TRACE - end' )                 );
  }

  pad_exit ();

?>