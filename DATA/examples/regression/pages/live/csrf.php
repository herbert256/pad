<?php

  // A live region on a page with $padCsrf on: the post of a click has no form to carry the
  // session's token, so the region holds it - data-pad-csrf - and the script sends it with
  // the event. The event posted with that token and the session cookie is answered; one
  // without the token is still turned away. The region carried no token before, and every
  // event was answered 403.

  $liveUrl  = $padHost . 'regression/pages/?live/counter&padInclude';
  $livePage = padCurl ( $liveUrl );
  $liveJar  = [ 'PHPSESSID' => $livePage ['cookies'] ['PHPSESSID'] ?? '' ];
  $liveCsrf = preg_match ( '/data-pad-csrf="([0-9a-f]{64})"/', $livePage ['data'], $liveMatch ) ? $liveMatch [1] : '';

  $liveEvent = [ 'padLive' => 'counter', 'padEvent' => 'add', 'padValue' => '41' ];

  $liveWith    = padCurl ( [ 'url' => $liveUrl, 'cookies' => $liveJar, 'post' => $liveEvent + [ 'padCsrfToken' => $liveCsrf ] ] );
  $liveWithout = padCurl ( [ 'url' => $liveUrl, 'cookies' => $liveJar, 'post' => $liveEvent ] );

  $liveResult = 'region carries a token: ' . ( $liveCsrf ? 'yes' : 'no' )
              . ', script sends it: ' . ( str_contains ( $livePage ['data'], "getAttribute('data-pad-csrf')" ) ? 'yes' : 'no' )
              . ', with it: '    . $liveWith ['result'] . ' ' . $liveWith ['data']
              . ', without it: ' . $liveWithout ['result'];

?>
