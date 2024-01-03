<?php


  function padTrackDbSession () {

    $session = $GLOBALS ['padSesID'];
    $request = $GLOBALS ['padReqID'];

    if ( padDb ( "check track_session where session='$session'" ) )
      padDb ( "update track_session set requests=requests+1 where session='$session'");
    else
      padDb ( "insert into track_session values('$session', NOW(), NOW(), 1)" );
   
    if ( ! $GLOBALS ['padTrackDbRequest'] )
      return;

    padDb ( "insert delayed into track_request
              values('{1}', '{2}', '{4:32}', NOW(), {5}, '{6}', '{7:32}', '{8}', '{9:1023}', '{10:1023}', '{11:1023}', '{12:1023}')
              ",
      [  1 => $session,
         2 => $request,
         4 => $GLOBALS ['padStartPage'] ?? '',
         5 => padDuration(),
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


  function padTrackDbData ( ) {

    global $padEtag, $padOutput;
    
    $etag = padDb ( "check track_data where etag='{1}'", [ 1 => $padEtag ] );

    if ( ! $etag )
      $session = padDb ( "insert into track_data values('{1}', '{2}')", [ 1 => $padEtag, 2=> $padOutput ] );

  }  


  function padTrackStart () {

   global $padLog;

   if ( function_exists ('getallheaders') )
      $headers = getallheaders();
    else
      $headers = [];

    padInfoFile ( "track/$padLog-entry.json",  [
        'headers' => $headers,
        'start'   => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0 ,    
        'pad' => [  'session'   => $GLOBALS ['padSesID'] ?? '',
                    'request'   => $GLOBALS ['padReqID'] ?? '',
                    'parent'    => $GLOBALS ['padRefID'] ?? '',
                    'page'      => $GLOBALS ['padPage'] ?? '' ],
        'get'     => $_GET ??    '',
        'post'    => $_POST ??   '',
        'data'    => file_get_contents('php://input') ?? '',
        'files '  => $_FILES ?? '',
        'cookies' => $_COOKIE ?? ''
    ] );

  }


  function padTrackEnd () {

    global $padLog;

    $phpHeaders = headers_list ()         ?? [];
    $padHeaders = $GLOBALS ['padHeaders'] ?? [];

    foreach ( $padHeaders as $header ) {
      $key = array_search ( $header, $phpHeaders );
      if ( $key !== FALSE )
        unset ( $phpHeaders [$key] );
    }

    $session =  [
        'http'       => http_response_code () ?? 'null',
        'length'     => $GLOBALS ['padLen'] ?? 0,
        'session'    => $GLOBALS ['padSesID'] ?? '',
        'request'    => $GLOBALS ['padReqID'] ?? '',
        'stop'       => $GLOBALS ['padStop'] ?? '',
        'end'        => microtime(true),
        'etag'       => $GLOBALS ['padEtag'] ?? '',
        'phpHeaders' => $phpHeaders,
        'padHeaders' => $padHeaders
      ];

    if ( isset ( $GLOBALS ['padStatsUser'] ) ) {
        $session ['duration'] = padDuration ();
        $session ['system']   = $GLOBALS ['padStatsSystem'];
        $session ['user']     = $GLOBALS ['padStatsUser'];
    }

    $session ['data'] = $GLOBALS ['padOutput'];

    padInfoFile ( "track/$padLog-exit.json", $session );

    padInfoFile ( 
      "track/$padLog.json", 
        [ 'in'      => json_decode ( padInfoGet ( padData . "track/$padLog-entry.json" ) ), 
          'out'     => json_decode ( padInfoGet ( padData . "track/$padLog-exit.json"  ) ),
          'session' => $_SESSION ?? '',
          'server'  => $_SERVER ?? '',
          'getenv'  => getenv () ?? '' , 
          'request' => $_REQUEST ?? ''
        ] 
    );

    unlink ( padData . "track/$padLog-entry.json" );
    unlink ( padData . "track/$padLog-exit.json" );

  }


?>