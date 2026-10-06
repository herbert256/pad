<?php

  // With $padCsrf on, a live region carries the session's token, and an event posted with
  // it in the X-CSRF-Token header - what the region's script sends - is answered with the
  // region; one without it is refused. The region carried no token, the script sent none,
  // and every event of an application with CSRF protection was answered 403.

  $lcPage  = padCurl ( $padGoExt . 'request/livecsrf&padInclude' );
  $lcToken = preg_match ( '/data-pad-csrf="([0-9a-f]+)"/', $lcPage ['data'], $lcMatch ) ? $lcMatch [1] : '';
  $lcJar   = [ 'PHPSESSID' => $lcPage ['cookies'] ['PHPSESSID'] ?? '' ];

  $lcWith = padCurl ( [ 'url'     => $padGoExt . 'request/livecsrf',
                        'cookies' => $lcJar,
                        'headers' => [ 'X-CSRF-Token' => $lcToken ],
                        'post'    => [ 'padLive' => 'pad', 'padEvent' => 'say', 'padValue' => 'hello' ] ] );

  $lcWithout = padCurl ( [ 'url'     => $padGoExt . 'request/livecsrf',
                           'cookies' => $lcJar,
                           'post'    => [ 'padLive' => 'pad', 'padEvent' => 'say', 'padValue' => 'hello' ] ] );

  $lcResult = ( $lcToken !== '' ? 'token' : 'no token' )
            . ' | with it: ' . $lcWith ['result'] . ' ' . trim ( $lcWith ['data'] )
            . ' | without: ' . $lcWithout ['result'];

?>
