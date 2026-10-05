<?php

  // Post, redirect, get on the demo guestbook, on disposable data: a valid post is answered
  // with a redirect to a plain GET of the page, so a refresh cannot sign it again - and
  // that page, fetched with the cookies the redirect set, as a browser does, shows the
  // entry and the thanks, which travel as a flash message. It was answered in place, as
  // the response to the POST, and later thanked through a ?signed=1 in the url. The store
  // is put back as it was.
  //
  // The demo checks CSRF tokens, so the form is fetched first, the way a browser shows it
  // before it is sent: its token travels back with the session cookie that came with it.

  $gbStore    = DATA . 'demo/guestbook.json';
  $gbSnapshot = padFileGet ( $gbStore, '[]' );

  $gbForm  = padCurl ( $padHost . 'demo/?guestbook&padInclude' );
  $gbToken = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $gbForm ['data'], $gbMatch ) ? $gbMatch [1] : '';
  $gbJar   = [ 'PHPSESSID' => $gbForm ['cookies'] ['PHPSESSID'] ?? '' ];

  $gbPost = padCurl ( [ 'url'     => $padHost . 'demo/?guestbook',
                        'cookies' => $gbJar,
                        'options' => [ 'FOLLOWLOCATION' => FALSE ],
                        'post'    => [ 'action' => 'add', 'name' => 'regression', 'comment' => 'marker entry',
                                       'padCsrfToken' => $gbToken ] ] );

  $gbJar ['padFlash'] = $gbPost ['cookies'] ['padFlash'] ?? '';

  $gbCurl = padCurl ( [ 'url' => $gbPost ['info'] ['redirect_url'] ?? '', 'cookies' => $gbJar ] );

  padFilePut ( $gbStore, $gbSnapshot );

  $gbResult = $gbPost ['result']
            . ' redirected: ' . ( str_ends_with ( $gbPost ['info'] ['redirect_url'] ?? '', 'demo/?guestbook' ) ? 'yes' : 'no' )
            . ' thanked: '    . ( str_contains ( $gbCurl ['data'], 'Thank you for signing' ) ? 'yes' : 'no' )
            . ' shown: '      . ( str_contains ( $gbCurl ['data'], 'marker entry' ) ? 'yes' : 'no' );

?>
