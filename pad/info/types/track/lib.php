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


  function padTrackFileRequestStart () {

    padInfoFile ( "track/entry.json", padSessionStart () );

  }


  function padTrackFileRequestEnd () {

    padInfoFile ( "track/exit.json", padSessionEnd () );

  }


  function padTrackFileData ( ) {

    padInfoFile ( 'track/data.pad', $GLOBALS ['padOutput'] );

  }


?>