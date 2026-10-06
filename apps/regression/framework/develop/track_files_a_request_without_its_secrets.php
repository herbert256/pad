<?php

  // The track mode files what a request brought - its headers, cookies, form, body and
  // server variables - and what it got back. It filed them as they came: the password of
  // a posted form, the bearer token, an API key and the session cookies stood in clear in
  // a file every local user could read. The probe is posted with each of them, and the
  // file of that request has to hold none, readable by its owner alone.

  $secrets = [ 'zzPostedPassword', 'zzBearerToken', 'zzApiKey', 'zzSessionCookie' ];

  $r = padCurl ( [
    'url'     => $padHost . 'regression/info/?probe&padInclude',
    'post'    => 'user=bob&password=zzPostedPassword',
    'headers' => [ 'Authorization' => 'Bearer zzBearerToken', 'X-Api-Key' => 'zzApiKey' ],
    'cookies' => [ 'PHPSESSID' => 'zzSessionCookie' ]
  ] );

  $request = explode ( '-', $r ['headers'] ['PAD'] ?? '' ) [1] ?? '';
  $file    = DATA . "track/requests/$request.json";

  // The recorder finishes after the response has been flushed back, and last of the five
  // info modes this application runs - after a trace with every option on, which writes
  // hundreds of files. With the suite fetching a dozen pages at once, the xml case twelve
  // probes among them, that took more than ten seconds.

  for ( $settle = 0; $settle < 300 and ! file_exists ( $file ); $settle++ ) {
    usleep ( 100000 );
    clearstatcache ();
  }

  $text = file_exists ( $file ) ? file_get_contents ( $file ) : '';

  $vFiled   = ( $text !== '' )                                                   ? 'yes' : 'NO';
  $vSecrets = ( ! array_filter ( $secrets, fn ( $s ) => str_contains ( $text, $s ) ) ) ? 'none' : 'SOME';
  $vMode    = ( $text !== '' and ( fileperms ( $file ) & 0777 ) == 0600 )        ? 'yes' : 'NO';

?>
