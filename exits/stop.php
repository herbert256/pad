<?php

  $pad_stop = $GLOBALS['pad_stop'];
  $pad_len  = ( $pad_stop == 200 ) ? strlen($GLOBALS['pad_output']) : 0;

  if ( $GLOBALS['pad_track_db_session'] )
    pad_track_db_session ($pad_stop);

  if ( $GLOBALS['pad_track_file'] )
    pad_track_file ($pad_stop);

  if ( $GLOBALS['pad_track_vars'] )
    pad_track_vars ('');

  pad_trace ("pad/end", "app=" . $GLOBALS['app'] .  " page=" . $GLOBALS['page'] .  "  session=" . $GLOBALS['PADSESSID'] .  " request=" . $GLOBALS['PADREQID'], TRUE);

  pad_close_session ();

  $pad_ob = ob_get_clean();
  
  if ($pad_ob)
    if ($GLOBALS['pad_trace'] == 'none' or $GLOBALS['pad_trace'] == 'file')
      pad_error ("Illegal output: $pad_ob");
    else
      echo "<hr>Illegal output: $pad_ob";

  if ( ! isset($GLOBALS['pad_sent']) ) {

    $GLOBALS['pad_sent'] = TRUE;

    include PAD_HOME . 'exits/headers.php';

    if ( $GLOBALS['pad_trace'] == 'browser' or $GLOBALS['pad_trace'] == 'both' )
      echo "</pre><hr>";
  
    if ( $pad_stop == 200 )
      echo $GLOBALS['pad_output'];

    if ( $GLOBALS['pad_trace'] == 'browser' or $GLOBALS['pad_trace'] == 'both' )
      echo "<hr>";

  }  

  pad_exit ();

?>