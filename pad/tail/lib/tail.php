<?php


  function padTailPadOccur () {

    global $pad, $padOccur;

    $return = $pad;

    $occur = $padOccur [$pad] ?? 0;

    if ( $occur <> 0 and $occur <> 99999 )
      $return .= "/$occur";

    return $return;

  }


  function padTail ( $parm1, $parm2='', $parm3='', $parm4='' ) {

    global $padTailCnt, $pad, $padOccur, $padTail, $padTailId, $padXrefId, $padTraceLine, $padXmlId;

    if ( ! $padTail )
      return;

    $padTailCnt++;

    $line = sprintf ( '%-7s',  $padTailCnt )
          . sprintf ( '%-8s', padTailPadOccur () )
          . sprintf ( '%-14s', "$padTraceLine/$padXrefId/$padXmlId" )
          . sprintf ( '%-15s', $parm1 )
          . padMakeSafe ( "$parm2 $parm3 $parm4", 100 );

    padTailLine ( "tail/" . $padTailId . '.txt', $line );

  }


  function padTailPut ( $in, $data, $append=0 ) {

    if ( $append ) padTailLine ( $in, $data );
    else           padTailFile ( $in, $data );

  }


  function padTailExists ( $file ) {

    return file_exists ( $file );

  }


  function padTailLine ( $in, $data ) {

    padTailCheck ( $in );

    file_put_contents ( padData . $in, $data . "\n", LOCK_EX | FILE_APPEND );
    
  }


  function padTailFile ( $in, $data ) {

    padTailCheck ( $in );

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);
    
    file_put_contents ( padData . $in, "$data\n", LOCK_EX );
    
  }


  function padTailCheck ( $file ) {

    $file = padData . $file;

    $dir = substr ( $file, 0, strrpos($file, '/') );
    
    if ( ! file_exists ($dir) )
      mkdir ($dir, $GLOBALS ['padDirMode'], true );

    if ( ! file_exists ($file) ) {      
      touch($file);
      chmod($file, $GLOBALS ['padFileMode']);
    }
    
  }


  function padTailGet ( $file ) {

    if ( ! file_exists ($file) )
      return '';

    return file_get_contents ($file);

  }


  function padRequestInit ( $file = '' ) {
    
    global $padTailId;

    $padTrackCallId = hrtime (true);

    if ( ! $file )
      $file = "request/" . $GLOBALS['padPage'] . "/$padTailId-entry.json";

    if ( function_exists ('getallheaders') )
      $headers = getallheaders();
    else
      $headers = [];

    padTailPut ( $file,  [
        'get'     => $_GET ??    '',
        'post'    => $_POST ??   '',
        'cookies' => $_COOKIE ?? '',
        'input'   => file_get_contents('php://input') ?? '',
        'headers' => $headers,
        'files '  => $_FILES ?? '',
        'server'  => $_SERVER ?? '',
        'request' => $_REQUEST ?? '',
        'session' => $_SESSION ?? '',
        '_ENV'    => $_ENV ?? '',
        'getenv'  => getenv () ?? '' ] );
      
  }


  function padRequestExit ( $file = '' ) {

    global $padTailId, $padOutput;

    if ( ! $file )
      $file = "request/" . $GLOBALS['padPage'] . "/$padTailId-exit.json";

    padTailPut ( $file,  [
        'headers' => headers_list () ?? [],
        'output'  => $padOutput      ?? ''
      ] );
      
  }


  function padSessionInfoStart () {

    return [
        'start'     => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0,
        'session'   => $GLOBALS ['padSesID'] ?? '',
        'request'   => $GLOBALS ['padReqID'] ?? '',
        'parent'    => $GLOBALS ['padRefID'] ?? '',
        'page'      => $GLOBALS ['padPage'] ?? '',
        'uri'       => $_SERVER ['REQUEST_URI']     ?? '' ,
        'referer'   => $_SERVER ['HTTP_REFERER']    ?? '' ,
        'remote'    => $_SERVER ['REMOTE_ADDR']     ?? '' ,
        'agent'     => $_SERVER ['HTTP_USER_AGENT'] ?? '',
        'cookies'   => $_SERVER ['HTTP_COOKIE']     ?? ''
      ];

  }


  function padSessionInfoEnd () {

    return [
        'session'   => $GLOBALS ['padSesID'] ?? '',
        'request'   => $GLOBALS ['padReqID'] ?? '',
        'duration'  => padDuration (),
        'length'    => $GLOBALS ['padLen'] ?? 0,
        'stop'      => $GLOBALS ['padStop'] ?? '',
        'etag'      => $GLOBALS ['padEtag'] ?? '',
        'end'       => microtime(true)
      ];

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
              values('{1}', '{2}', '{4:32}', NOW(), {5}, '{6}', '{7:32}', '{8}', '{9:1023}', '{10:1023}', '{11:1023}', '{12:1023}')",
      [  1 => $session,
         2 => $request,
         4 => $GLOBALS ['padPage'] ?? '',
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


  function padTrackFileRequestStart () {

    global $padTailId;

    padTailPut ( "track/$padTailId-entry.json", padSessionInfoEnd () );

  }


  function padTrackFileRequestEnd () {

    global $padTailId;

    padTailPut ( "track/$padTailId-exit.json", padSessionInfoEnd () );

  }


  function padTrackFileData ( ) {

    global $padEtag, $padOutput;

    $file = "output/$padEtag.pad";

    if ( ! padExists(padData . $file) )
      padTailPut ($file, $padOutput);

  }


  function padTrackDbData ( ) {

    global $padEtag, $padOutput;
    
    $etag = padDb ( "check track_data where etag='{1}'", [ 1 => $padEtag ] );

    if ( ! $etag )
      $session = padDb ( "insert into track_data values('{1}', '{2}')", [ 1 => $padEtag, 2=> $padOutput ] );

  }  


?>