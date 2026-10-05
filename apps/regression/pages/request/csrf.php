<?php

  // CSRF protection on the demo, which switches $padCsrf on: a POST without the session's
  // token is turned away with 403 before the page runs, and so is one with a token of its
  // own making; the token the form carried, sent back with the session cookie - in the
  // field or in the X-CSRF-Token header - lets the post through to the page, which answers
  // an empty contact form with its own errors and stores nothing. The demo answered every
  // post before, from any site.

  $csrfForm  = padCurl ( $padHost . 'demo/?contact&padInclude' );
  $csrfToken = preg_match ( '/name="padCsrfToken" value="([0-9a-f]{64})"/', $csrfForm ['data'], $csrfMatch ) ? $csrfMatch [1] : '';
  $csrfJar   = [ 'PHPSESSID' => $csrfForm ['cookies'] ['PHPSESSID'] ?? '' ];

  $csrfPost = fn ( $fields, $cookies = [], $headers = [] ) =>
    padCurl ( [ 'url'     => $padHost . 'demo/?contact&padInclude',
                'post'    => $fields + [ 'padForm' => 'contact' ],
                'cookies' => $cookies,
                'headers' => $headers ] );

  $csrfNone   = $csrfPost ( [] );
  $csrfWrong  = $csrfPost ( [ 'padCsrfToken' => str_repeat ( 'a', 64 ) ], $csrfJar );
  $csrfField  = $csrfPost ( [ 'padCsrfToken' => $csrfToken ], $csrfJar );
  $csrfHeader = $csrfPost ( [], $csrfJar, [ 'X-CSRF-Token' => $csrfToken ] );

  $csrfResult = 'form carries a token: ' . ( $csrfToken ? 'yes' : 'no' )
              . ', none: '   . $csrfNone   ['result'] . ' ' . ( str_contains ( $csrfNone ['data'], 'CSRF token is missing' ) ? 'says why' : 'silent' )
              . ', wrong: '  . $csrfWrong  ['result']
              . ', field: '  . $csrfField  ['result'] . ' ' . ( str_contains ( $csrfField  ['data'], 'Please correct the errors' ) ? 'page ran' : 'page did not run' )
              . ', header: ' . $csrfHeader ['result'] . ' ' . ( str_contains ( $csrfHeader ['data'], 'Please correct the errors' ) ? 'page ran' : 'page did not run' );

?>
