<?php

  // A visitor whose session holds the login keeps that session: the remember-me cookie is
  // read only for a visitor without one. Read on every request, it renewed the session id -
  // and dropped the CSRF token - each time, so the post of a form shown before another
  // request of the same visitor was refused. rememberform writes a token and an {auth}.

  $padAuthRemember = 'pagesRememberFind';

  padLogin ( pagesRememberFind ( 7 ), TRUE );

  $remember = [ padAuthCookieName () => $_COOKIE [ padAuthCookieName () ] ?? '' ];
  $url      = $padHost . 'regression/pages/?request/rememberform&padInclude';

  $first   = padCurl ( [ 'url' => $url, 'cookies' => $remember ] );
  $session = array_merge ( $remember, $first ['cookies'] ?? [] );

  $token   = explode ( ' ', trim ( $first ['data'] ?? '' ) ) [0];

  $second  = padCurl ( [ 'url' => $url, 'cookies' => $session ] );
  $renewed = isset ( $second ['cookies'] [ session_name () ] ) ? 'renewed' : 'kept';

  $posted  = padCurl ( [ 'url'     => $url,
                         'cookies' => array_merge ( $session, $second ['cookies'] ?? [] ),
                         'post'    => [ 'padCsrfToken' => $token ] ] );

  $answer = "the session $renewed, the post: " . $posted ['result'];

  padLogout ();

?>
