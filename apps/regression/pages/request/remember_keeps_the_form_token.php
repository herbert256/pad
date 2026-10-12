<?php

  // A visitor who comes back with only a remember-me cookie is logged in before anything of
  // the page is written: the login makes a new session, and made at the {auth} below a form
  // it dropped the CSRF token the form already held - the post of that form was refused.
  // rememberform writes a token and then an {auth}; the token goes back in a post.

  $padAuthRemember = 'pagesRememberFind';

  padLogin ( pagesRememberFind ( 7 ), TRUE );

  $remember = $_COOKIE [ padAuthCookieName () ] ?? '';
  $url      = $padHost . 'regression/pages/?request/rememberform&padInclude';

  $shown = padCurl ( [ 'url' => $url, 'cookies' => [ padAuthCookieName () => $remember ] ] );

  [ $token, $who ] = array_pad ( explode ( ' ', trim ( $shown ['data'] ?? '' ) ), 2, '' );

  $posted = padCurl ( [ 'url'     => $url,
                        'cookies' => [ padAuthCookieName () => $remember ] + ( $shown ['cookies'] ?? [] ),
                        'post'    => [ 'padCsrfToken' => $token ] ] );

  $answer = "shown to $who, the post: " . $posted ['result'];

  padLogout ();

?>
