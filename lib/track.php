<?php

  function padTrackFileRequest () {
    
    $dir = $GLOBALS ['padSesID'];
    $id  = $GLOBALS ['padReqID'];

    $track = [
        'session'   => $GLOBALS ['padSesID'] ?? '',
        'request'   => $GLOBALS ['padReqID'] ?? '',
        'reference' => $GLOBALS ['padRefID'] ?? '',
        'padApp'       => $GLOBALS ['padApp'] ?? '',
        'padPage'      => $GLOBALS ['padPage'] ?? '',
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

    padFilePutContents ( "track/$dir/$id.json", $track );
      
  }


  function padTrackDbSession () {

    $session = $GLOBALS ['padSesID'];
    $request = $GLOBALS ['padReqID'];

    if ( padDb ( "check track_session where session='$session'" ) )
      padDb ( "update track_session set requests=requests+1 where session='$session'");
    else
      padDb ( "insert into track_session values('$session', NOW(), NOW(), 1)" );
   
    if ( ! $GLOBALS ['padTrackDbRequest'] )
      return;

    padDb ( "insert into track_request
              values('{1}', '{2}', '{3:32}', '{4:32}', NOW(), {5}, '{6}', '{7:32}', '{8}', '{9:1023}', '{10:1023}', '{11:1023}', '{12:1023}')",
      [  1 => $session,
         2 => $request,
         3 => $GLOBALS ['padApp']  ?? '',
         4 => $GLOBALS ['padPage'] ?? '',
         5 => padDuration($_SERVER['REQUEST_TIME_FLOAT'] ?? 0),
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


  function padTrackFileData ( $padEtag, $padOutput ) {

    $file = "output/$padEtag.html";

    if ( ! padExists(padData . $file) )
      padFilePutContents ($file, $padOutput);

  }

  function padTrackDbData ( $padEtag, $padOutput ) {
    
    $etag = padDb ( "check track_data where etag='{1}'", [ 1 => $padEtag ] );

    if ( ! $etag )
      $session = padDb ( "insert into track_data values('{1}', '{2}')", [ 1 => $padEtag, 2=> $padOutput ] );

  }


?>