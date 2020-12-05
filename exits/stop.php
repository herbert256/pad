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
  if ($pad_ob) {
    echo "Illegal output<hr><pre>$pad_ob</pre><hr>";
    pad_exit();
  }  

  if ( ! isset($GLOBALS['pad_sent']) ) {

    $GLOBALS['pad_sent'] = TRUE;

    include PAD_HOME . 'exits/headers.php';

    if ( $pad_stop == 200 )
      echo $GLOBALS['pad_output'];

  }  

  pad_exit ();

  function pad_track_db_session ($pad_stop) {

    $session = pad_db( "field id from track_session where sessionid='{1}'", [ 1 => $GLOBALS['PADSESSID'] ] );

    if ( ! $session )
      $session = pad_db ( "insert into track_session values(NULL, '{1}', NOW(), NOW(), 1)", [ 1 => $GLOBALS['PADSESSID'] ] );
    else
      pad_db ( "update track_session set requests=requests+1 where id=$session");
   
    if ( ! $GLOBALS['pad_track_db_request'] )
      return;

    pad_db ( "insert into track_request
              values(NULL, {1}, '{2:32}', '{3:32}', NOW(), '{4}', {5}, '{6}', '{7:32}', '{8:32}', '{9:1023}', '{10:1023}', '{11:1023}', '{12:1023}', '{13:1023}', '{14:25}')",
      [  1 => $session,
         2 => $GLOBALS['app']  ?? '',
         3 => $GLOBALS['page'] ?? '',
         4 => pad_duration($_SERVER['REQUEST_TIME_FLOAT'] ?? 0),
         5 => $GLOBALS['pad_len'] ?? 0,
         6 => $pad_stop ?? '',
         8 => $GLOBALS['pad_etag'] ?? $GLOBALS['pad_cache_etag'] ?? '',
         9 => $_SERVER ['REQUEST_URI']     ?? '' ,
        10 => $_SERVER ['HTTP_REFERER']    ?? '' ,
        11 => $_SERVER ['REMOTE_ADDR']     ?? '' ,
        12 => $_SERVER ['HTTP_USER_AGENT'] ?? '' ,
        13 => $_SERVER ['HTTP_COOKIE']     ?? '' ,
        14 => pad_id ()
      ]
    );
      
  }

  function pad_track_file ($pad_stop) {
    
    $id = pad_id ();
  
    $track = [
        'session'   => $GLOBALS ['PADSESSID'] ?? '',
        'request'   => $GLOBALS ['PADREQID'] ?? '',
        'reference' => $GLOBALS ['PADREFID '] ?? '',
        'app'       => $GLOBALS ['app'] ?? '',
        'page'      => $GLOBALS ['page'] ?? '',
        'start'     => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0,
        'end'       => microtime(true),
        'length'    => $GLOBALS ['pad_len'] ?? 0,
        'stop'      => $pad_stop ?? '',
        'etag'      => $GLOBALS ['pad_etag'] ?? '',
        'uri'       => $_SERVER ['REQUEST_URI']     ?? '' ,
        'referer'   => $_SERVER ['HTTP_REFERER']    ?? '' ,
        'remote'    => $_SERVER ['REMOTE_ADDR']     ?? '' ,
        'agent'     => $_SERVER ['HTTP_USER_AGENT'] ?? '',
        'cookies  ' => $_SERVER ['HTTP_COOKIE']     ?? ''
      ];

    $json = json_encode($track, JSON_PARTIAL_OUTPUT_ON_ERROR);
    
    pad_file_put_contents ( "track/$id.json", $json, 1);
      
  }


  function pad_close_session () {

    if ( ! isset($GLOBALS['pad_session_started']) )
      return;

    foreach ( $GLOBALS ['pad_session_vars'] as $var )
      if ( isset ( $GLOBALS [$var] ) )
        $_SESSION [$var] = $GLOBALS [$var];

    session_write_close ();

  }


?>