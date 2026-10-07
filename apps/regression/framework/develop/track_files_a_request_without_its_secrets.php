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
  // hundreds of files. It is done when the request file is there and the entry file it was
  // made from is gone: the file alone was looked at the moment it appeared, and its mode
  // then was not always its last. Within the 30 seconds PHP gives this page.

  $entry = DATA . "track/requests/$request-entry.json";

  for ( $settle = 0; $settle < 250 and ( ! file_exists ( $file ) or file_exists ( $entry ) ); $settle++ ) {
    usleep ( 100000 );
    clearstatcache ();
  }

  $text = file_exists ( $file ) ? file_get_contents ( $file ) : '';

  // Owner only from the first moment: the files of the track directory were written 0644
  // and made 0600 after - and padFilePut's temporary file beside each is the umask's - so
  // for that moment any local user could read them. The directory itself is 0700 now.

  $vFiled   = ( $text !== '' )                                                   ? 'yes' : 'NO';
  $vSecrets = ( ! array_filter ( $secrets, fn ( $s ) => str_contains ( $text, $s ) ) ) ? 'none' : 'SOME';
  $vMode    = ( $text !== '' and ( fileperms ( $file ) & 0777 ) == 0600
                and ( fileperms ( DATA . 'track' ) & 0777 ) == 0700 )            ? 'yes' : 'NO';

?>
