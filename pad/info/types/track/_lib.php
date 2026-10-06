<?php

  // The writers of the 'track' info mode, loaded by info/types/track/start.php and called from
  // its start and end files.
  //
  // padInfoTrackDbSession  counts the request against $padSesID in track_session and, when
  //                        $padInfoTrackDbRequest is on, adds a track_request row with the page,
  //                        duration, length, status, etag, URI, referer, address and user agent
  // padInfoTrackDbData     stores the response body in track_data once per distinct $padEtag
  // padInfoTrackStart      dumps everything that came in to DATA/track/requests/<log>-entry.json
  // padInfoTrackEnd        rewrites that as <log>.json with the response added - status, PAD and
  //                        PHP headers kept apart, length and body - and drops the entry file
  // padInfoTrackData       saves the bare page under DATA/track/data/complete or /include
  //
  // All database work goes through padDb, so it lands in the PAD database, not the app's.

  function padInfoTrackDbSession () {

    global $padEtag, $padInfoTrackDbRequest, $padLen, $padReqID, $padSesID, $padStartPage, $padStop;

    $session = $padSesID;
    $request = $padReqID;

    if ( padDb ( "check track_session where session='{1}'", [ 1 => $session ] ) )
      padDb ( "update track_session set requests=requests+1 where session='{1}'", [ 1 => $session ] );
    else
      padDb ( "insert into track_session values('{1}', NOW(), NOW(), 1)", [ 1 => $session ] );

    if ( ! $padInfoTrackDbRequest )
      return;

    padDb ( "insert delayed into track_request
             values('{1}', '{2}', '{4:32}', NOW(), {5}, '{6}', '{7:32}', '{8}', '{9:1023}', '{10:1023}', '{11:1023}', '{12:1023}')
            ",
      [  1 => $session,
         2 => $request,
         4 => $padStartPage ?? '',
         5 => padDuration (),
         6 => $padLen ?? 0,
         7 => $padStop ?? '',
         8 => $padEtag ?? '',
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

  // What came in is filed as an error report keeps it (padRedact): the passwords of a posted
  // form, an Authorization header, an API key and the session cookies stood in clear in a
  // file anyone who could read DATA could read. Cookies keep their names, the body is kept
  // as redacted form or JSON fields or by its size alone, and the environment - where the
  // server keeps its own secrets - is left out.

  function padInfoTrackStart () {

   global $padLog;

    if ( function_exists ('getallheaders') ) $headers = getallheaders() ?? [];
    else                                     $headers = [];

    padInfoTrackPut ( "track/requests/$padLog-entry.json",  [
        'headers' => padRedact ( $headers,        '',        TRUE ),
        'get'     => padRedact ( $_GET    ?? [], '_GET',    TRUE ),
        'post'    => padRedact ( $_POST   ?? [], '_POST',   TRUE ),
        'files'   => padRedact ( $_FILES  ?? [], '_FILES',  TRUE ),
        'cookies' => padRedact ( $_COOKIE ?? [], '_COOKIE', TRUE ),
        'data'    => padDumpInputKept () [0],
        'server'  => padRedact ( $_SERVER ?? [], '_SERVER', TRUE )
    ] );

  }

  // Owner only, as the error reports are (padDumpFilePut).

  function padInfoTrackPut ( $file, $data ) {

    padFilePut ( $file, $data );

    @chmod ( DATA . $file, 0600 );

  }

  function padInfoTrackEnd () {

    global $padHeaders, $padLen, $padLog, $padOutput, $padStop;

    if ( function_exists ('http_response_code') )  $http = http_response_code ();
    else                                           $http = $padStop ?? 0;

    if ( function_exists ('headers_list') )        $phpHeaders = headers_list () ?? [];
    else                                           $phpHeaders = [];

    $padHeaders = $padHeaders ?? [];

    foreach ( $padHeaders as $header ) {
      $key = array_search ( $header, $phpHeaders );
      if ( $key !== FALSE )
        unset ( $phpHeaders [$key] );
    }

    padInfoTrackPut (
      "track/requests/$padLog.json",
        [ 'pad' => padRedact ( padInfo (), '', TRUE ),
          'in'  => json_decode ( padInfoGet ( DATA . "track/requests/$padLog-entry.json" ) ),
          'out' => padRedact ( [
             'http'    => $http,
             'headers' => [
               'php'     => $phpHeaders,
               'pad'     => $padHeaders ],
             'length'  => $padLen   ?? '',
             'data'    => $padOutput,
            ], '', TRUE )
        ]
    );

    unlink ( DATA . "track/requests/$padLog-entry.json" );

  }

  function padInfoTrackData () {

    global $padOutput, $padStartPage;

    if ( padInclude () )
      $dir = 'include';
    else
      $dir = 'complete';

    padInfoTrackPut ( "track/data/$dir/$padStartPage.html", padRedactText ( (string) $padOutput ) );

  }

?>
