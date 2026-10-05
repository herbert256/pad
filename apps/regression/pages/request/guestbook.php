<?php

  // Post, redirect, get on the demo guestbook, on disposable data: a valid post is answered
  // with a redirect to a plain GET of the page - followed here as a browser would - which
  // shows the entry and the thank-you, so a refresh cannot sign it again. It was answered
  // in place, as the response to the POST. The store is put back as it was.
  //
  // The demo checks CSRF tokens, so the form is fetched first, the way a browser shows it
  // before it is sent: its token travels back with the session cookie that came with it.

  $gbStore    = DATA . 'demo/guestbook.json';
  $gbSnapshot = padFileGet ( $gbStore, '[]' );

  $gbForm  = padCurl ( $padHost . 'demo/?guestbook&padInclude' );
  $gbToken = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $gbForm ['data'], $gbMatch ) ? $gbMatch [1] : '';

  $gbCurl = padCurl ( [ 'url'     => $padHost . 'demo/?guestbook',
                        'cookies' => [ 'PHPSESSID' => $gbForm ['cookies'] ['PHPSESSID'] ?? '' ],
                        'post'    => [ 'action' => 'add', 'name' => 'regression', 'comment' => 'marker entry',
                                       'padCsrfToken' => $gbToken ] ] );

  padFilePut ( $gbStore, $gbSnapshot );

  $gbResult = $gbCurl ['result']
            . ' redirected: ' . ( str_contains ( $gbCurl ['info'] ['url'] ?? '', 'signed=1' ) ? 'yes' : 'no' )
            . ' thanked: '    . ( str_contains ( $gbCurl ['data'], 'Thank you for signing' ) ? 'yes' : 'no' )
            . ' shown: '      . ( str_contains ( $gbCurl ['data'], 'marker entry' ) ? 'yes' : 'no' );

?>
