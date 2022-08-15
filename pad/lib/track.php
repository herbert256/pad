 <?php


  function pTrack () {
  
    return [
        'session'   => $GLOBALS ['PADSESSID'] ?? '',
        'request'   => $GLOBALS ['PADREQID'] ?? '',
        'reference' => $GLOBALS ['PADREFID '] ?? '',
        'app'       => $GLOBALS ['app'] ?? '',
        'page'      => $GLOBALS ['page'] ?? '',
        'start'     => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0,
        'end'       => microtime(true),
        'length'    => $GLOBALS ['padLen'] ?? 0,
        'stop'      => $GLOBALS ['padStop'] ?? '',
        'etag'      => $GLOBALS ['padEtag'] ?? '',
        'uri'       => $_SERVER ['REQUEST_URI']     ?? '' ,
        'referer'   => $_SERVER ['HTTP_REFERER']    ?? '' ,
        'remote'    => $_SERVER ['REMOTE_ADDR']     ?? '' ,
        'agent'     => $_SERVER ['HTTP_USER_AGENT'] ?? '',
        'cookies'   => $_SERVER ['HTTP_COOKIE']     ?? ''
      ];
      
  }


  function pTrack_file_request () {
    
    $id    = $GLOBALS ['PADREQID'];
    $track = pTrack ();
    $json  = pJson ($track);
    
    pFile_put_contents ( "track/$id.json", $json, 1);
      
  }


  function pTrack_file_data () {

    global $padEtag, $padOutput;
    
    $padContentStore_file = "output/$padEtag.html";

    if ( ! file_exists(DATA . "$padContentStore_file") )
      pFile_put_contents ($padContentStore_file, $padOutput);

  }


  function pTrack_db_session () {

    $session = $GLOBALS ['PADSESSID'];
    $request = $GLOBALS ['PADREQID'];

    if ( pDb ( "check track_session where session='$session'" ) )
      pDb ( "update track_session set requests=requests+1 where session='$session'");
    else
      pDb ( "insert into track_session values('$session', NOW(), NOW(), 1)" );
   
    if ( ! $GLOBALS ['padTrack_db_request'] )
      return;

    pDb ( "insert into track_request
              values('{1}', '{2}', '{3:32}', '{4:32}', NOW(), {5}, '{6}', '{7:32}', '{8}', '{9:1023}', '{10:1023}', '{11:1023}', '{12:1023}')",
      [  1 => $session,
         2 => $request,
         3 => $GLOBALS ['app']  ?? '',
         4 => $GLOBALS ['page'] ?? '',
         5 => pDuration($_SERVER['REQUEST_TIME_FLOAT'] ?? 0),
         6 => $GLOBALS ['padLen'] ?? 0,
         7 => $GLOBALS ['padStop'] ?? '',
         8 => $GLOBALS ['padEtag'] ?? '',
         9 => $_SERVER ['REQUEST_URI']     ?? '' ,
        10 => $_SERVER ['HTTP_REFERER']    ?? '' ,
        11 => $_SERVER ['REMOTE_ADDR']     ?? '' ,
        12 => $_SERVER ['HTTP_USER_AGENT'] ?? ''
      ]
    );
      
  }


  function pTrack_db_data () {

    global $padEtag, $padOutput;
    
    $etag = pDb ( "check track_data where etag='{1}'", [ 1 => $padEtag ] );

    if ( ! $etag )
      $session = pDb ( "insert into track_data values('{1}', '{2}')", [ 1 => $padEtag, 2=> $padOutput ] );

  }


?>