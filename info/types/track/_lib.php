<?php


  function padInfoTrackDbSession () {

    $session = $GLOBALS ['padSesID'];
    $request = $GLOBALS ['padReqID'];

    if ( padDb ( "check track_session where session='$session'" ) )
      padDb ( "update track_session set requests=requests+1 where session='$session'");
    else
      padDb ( "insert into track_session values('$session', NOW(), NOW(), 1)" );
   
    if ( ! $GLOBALS ['padInfoTrackDbRequest'] )
      return;

    padDb ( "insert delayed into track_request
             values('{1}', '{2}', '{4:32}', NOW(), {5}, '{6}', '{7:32}', '{8}', '{9:1023}', '{10:1023}', '{11:1023}', '{12:1023}')
            ",
      [  1 => $session,
         2 => $request,
         4 => $GLOBALS ['padStartPage'] ?? '',
         5 => padDuration (),
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


  function padInfoTrackDbData ( ) {

    global $padEtag, $padOutput;
    
    $etag = padDb ( "check track_data where etag='{1}'", [ 1 => $padEtag ] );

    if ( ! $etag )
      $session = padDb ( "insert into track_data values('{1}', '{2}')", [ 1 => $padEtag, 2=> $padOutput ] );

  }  


  function padInfoTrackStart () {

   global $padLog;

    if ( function_exists ('getallheaders') ) $headers = getallheaders() ?? [];
    else                                     $headers = [];

    padFilePut ( "track/requests/$padLog-entry.json",  [
        'headers' => getallheaders() ?? '',
        'get'     => $_GET    ?? '',
        'post'    => $_POST   ?? '',
        'files '  => $_FILES  ?? '',
        'cookies' => $_COOKIE ?? '',
        'data'    => padFileGet('php://input') ?? '',
        'server'  => $_SERVER ?? '',
        'host'    => $_ENV ?? ''
    ] );

  }


  function padInfoTrackEnd () {

    global $padLog;

    if ( function_exists ('http_response_code') )  $http = http_response_code ();
    else                                           $http = $GLOBALS ['padStop'] ?? 0;

    if ( function_exists ('headers_list') )        $phpHeaders = headers_list () ?? [];
    else                                           $phpHeaders = [];

    $padHeaders = $GLOBALS ['padHeaders'] ?? [];

    foreach ( $padHeaders as $header ) {
      $key = array_search ( $header, $phpHeaders );
      if ( $key !== FALSE )
        unset ( $phpHeaders [$key] );
    }

    padFilePut ( 
      "track/requests/$padLog.json", 
        [ 'pad' => padInfo (),
          'in'  => json_decode ( padInfoGet ( DAT . "track/requests/$padLog-entry.json" ) ),
          'out' => [
             'http'    => $http,
             'headers' => [
               'php'     => $phpHeaders,
               'pad'     => $padHeaders ], 
             'length'  => $GLOBALS ['padLen']   ?? '',
             'data'    => $GLOBALS ['padOutput'],
            ]
        ] 
    );

    unlink ( DAT . "track/requests/$padLog-entry.json" );

  }


  function padInfoTrackData () {

    global $padStartPage;
    
    if ( padInclude () )
      $dir = 'include';
    else
      $dir = 'complete';

    padFilePut ( "track/data/$dir/$padStartPage.html", $GLOBALS ['padOutput'] );

  }


?>