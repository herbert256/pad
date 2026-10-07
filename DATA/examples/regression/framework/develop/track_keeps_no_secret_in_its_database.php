<?php

  // The track mode's files are redacted; its database rows were written as they came: the
  // page in track_data with the CSRF token of its form, the address in track_request with
  // the token of ?reset&token=... and the referer with a password. The writers are run here
  // on a page and a request made for the case, and the rows read back - then removed.

  include_once PAD . 'info/types/track/_lib.php';

  $token = padCsrfToken ();

  $trackKeep = [ $padOutput ?? '', $padEtag ?? '', $_SERVER ['REQUEST_URI'] ?? '', $_SERVER ['HTTP_REFERER'] ?? NULL, $padInfoTrackDbRequest ?? FALSE ];

  $padOutput                = "<form method=\"post\"><input type=\"hidden\" name=\"padCsrfToken\" value=\"$token\"></form>";
  $padEtag                  = 'zz' . padRandomString ( 20 );
  $_SERVER ['REQUEST_URI']  = '/pad/shop/?reset&token=zzDbQueryToken';
  $_SERVER ['HTTP_REFERER'] = 'http://example.com/?password=zzDbRefererPass';
  $padInfoTrackDbRequest    = TRUE;

  padInfoTrackDbData ();
  padInfoTrackDbSession ();

  $trackData = (string) padDb ( "field data from track_data where etag='{1}'", [ 1 => $padEtag ] );

  for ( $settle = 0; $settle < 20; $settle++ ) {
    $trackRow = padDb ( "record url, ref from track_request where session='{1}' and request='{2}'", [ 1 => $padSesID, 2 => $padReqID ] );
    if ( $trackRow )
      break;
    usleep ( 100000 );
  }

  $trackSeen = json_encode ( [ str_contains ( $trackData, $token ), str_contains ( json_encode ( $trackRow ), 'zzDb' ), $trackData !== '', (bool) $trackRow ] );

  padDb ( "delete from track_data where etag='{1}'", [ 1 => $padEtag ] );
  padDb ( "delete from track_request where session='{1}' and request='{2}'", [ 1 => $padSesID, 2 => $padReqID ] );

  [ $padOutput, $padEtag, $_SERVER ['REQUEST_URI'], $_SERVER ['HTTP_REFERER'], $padInfoTrackDbRequest ] = $trackKeep;

?>
