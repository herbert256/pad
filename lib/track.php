<?php


  function pad_track () {
  
    return [
        'session'   => $GLOBALS ['PADSESSID'] ?? '',
        'request'   => $GLOBALS ['PADREQID'] ?? '',
        'reference' => $GLOBALS ['PADREFID '] ?? '',
        'app'       => $GLOBALS ['app'] ?? '',
        'page'      => $GLOBALS ['page'] ?? '',
        'start'     => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0,
        'end'       => microtime(true),
        'length'    => $GLOBALS ['pad_len'] ?? 0,
        'stop'      => $GLOBALS ['pad_stop'] ?? '',
        'etag'      => $GLOBALS ['pad_etag'] ?? '',
        'uri'       => $_SERVER ['REQUEST_URI']     ?? '' ,
        'referer'   => $_SERVER ['HTTP_REFERER']    ?? '' ,
        'remote'    => $_SERVER ['REMOTE_ADDR']     ?? '' ,
        'agent'     => $_SERVER ['HTTP_USER_AGENT'] ?? '',
        'cookies'   => $_SERVER ['HTTP_COOKIE']     ?? ''
      ];
      
  }


  function pad_track_file_request () {
    
    $id    = $GLOBALS['PADREQID'];
    $track = pad_track ();
    $json  = pad_json ($track);
    
    pad_file_put_contents ( "track/$id.json", $json, 1);
      
  }


  function pad_track_file_data () {

    global $pad_etag, $pad_output;
    
    $pad_content_store_file = "output/$pad_etag.html";

    if ( ! pad_file_exists(PAD_DATA . "$pad_content_store_file") )
      pad_file_put_contents ($pad_content_store_file, $pad_output);

  }


  function pad_track_db_session () {

    $session = $GLOBALS['PADSESSID'];
    $request = $GLOBALS['PADREQID'];

    if ( pad_db ( "check track_session where session='$session'" ) )
      pad_db ( "update track_session set requests=requests+1 where session='$session'");
    else
      pad_db ( "insert into track_session values('$session', NOW(), NOW(), 1)" );
   
    if ( ! $GLOBALS['pad_track_db_request'] )
      return;

    pad_db ( "insert into track_request
              values('{1}', '{2}', '{3:32}', '{4:32}', NOW(), {5}, '{6}', '{7:32}', '{8}', '{9:1023}', '{10:1023}', '{11:1023}', '{12:1023}')",
      [  1 => $session,
         2 => $request,
         3 => $GLOBALS['app']  ?? '',
         4 => $GLOBALS['page'] ?? '',
         5 => pad_duration($_SERVER['REQUEST_TIME_FLOAT'] ?? 0),
         6 => $GLOBALS['pad_len'] ?? 0,
         7 => $GLOBALS['pad_stop'] ?? '',
         8 => $GLOBALS['pad_etag'] ?? '',
         9 => $_SERVER ['REQUEST_URI']     ?? '' ,
        10 => $_SERVER ['HTTP_REFERER']    ?? '' ,
        11 => $_SERVER ['REMOTE_ADDR']     ?? '' ,
        12 => $_SERVER ['HTTP_USER_AGENT'] ?? ''
      ]
    );
      
  }


  function pad_track_db_data () {

    global $pad_etag, $pad_output;
    
    $etag = pad_db ( "check track_data where etag='{1}'", [ 1 => $pad_etag ] );

    if ( ! $etag )
      $session = pad_db ( "insert into track_data values('{1}', '{2}')", [ 1 => $pad_etag, 2=> $pad_output ] );

  }


?>