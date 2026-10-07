<?php

  // The track mode counts a session's requests in track_session: it asked whether the row
  // was there and inserted it when not, so requests of one new session arriving together
  // each found no row, each inserted it, and all but the first died on the duplicate key -
  // after their answer had gone out, with nothing filed. Twelve probes of one fresh session
  // are fetched together, and each must have its request file.

  // Asked under the other spelling of this host: a fetch of $padHost itself carries this
  // request's own session, an old one.

  $other   = str_contains ( $padHost, '//127.0.0.1' ) ? str_replace ( '//127.0.0.1', '//localhost', $padHost )
                                                      : str_replace ( '//localhost', '//127.0.0.1', $padHost );
  $session = padRandomString ( 8 );
  $inputs  = [];

  for ( $i = 0; $i < 12; $i++ )
    $inputs [] = [ 'url' => $other . 'regression/info/?probe&padInclude', 'cookies' => [ 'padSesID' => $session ] ];

  $files = [];

  foreach ( padCurlMulti ( $inputs, 12 ) as $curl )
    $files [] = DATA . 'track/requests/' . ( explode ( '-', $curl ['headers'] ['PAD'] ?? '' ) [1] ?? '' ) . '.json';

  for ( $settle = 0; $settle < 150; $settle++ ) {
    clearstatcache ();
    if ( count ( array_filter ( $files, 'file_exists' ) ) == 12 )
      break;
    usleep ( 100000 );
  }

  $filed = count ( array_filter ( $files, 'file_exists' ) );

?>
