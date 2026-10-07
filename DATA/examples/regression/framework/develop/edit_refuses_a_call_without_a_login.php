<?php

  // With the session's token but without a login, a call is refused by the guard: the
  // login page hands out a session and its token, the call brings both back.

  $login = padCurl ( $padHost . 'edit/?login&padInclude' );
  $token = preg_match ( '/name="padCsrfToken" value="([0-9a-f]{64})"/', $login ['data'], $m ) ? $m [1] : '';

  $curl  = padCurl ( [ 'url'     => $padHost . 'edit/?api&action=apps&padFormat=json',
                       'post'    => [ 'padCsrfToken' => $token ],
                       'cookies' => [ 'PHPSESSID' => $login ['cookies'] ['PHPSESSID'] ?? '' ] ] );

  $answer = ( $token === '' ? 'no token - ' : '' ) . $curl ['result'] . ': ' . trim ( $curl ['data'] );

?>
