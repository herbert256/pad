<?php

  // Post, redirect, get on the demo guestbook, on disposable data: a valid post is answered
  // with a redirect to a plain GET of the page - followed here as a browser would - which
  // shows the entry and the thank-you, so a refresh cannot sign it again. It was answered
  // in place, as the response to the POST. The store is put back as it was.

  $gbStore    = DATA . 'demo/guestbook.json';
  $gbSnapshot = padFileGet ( $gbStore, '[]' );

  $gbCurl = padCurl ( [ 'url'  => $padHost . 'demo/?guestbook',
                        'post' => [ 'action' => 'add', 'name' => 'regression', 'comment' => 'marker entry' ] ] );

  padFilePut ( $gbStore, $gbSnapshot );

  $gbResult = $gbCurl ['result']
            . ' redirected: ' . ( str_contains ( $gbCurl ['info'] ['url'] ?? '', 'signed=1' ) ? 'yes' : 'no' )
            . ' thanked: '    . ( str_contains ( $gbCurl ['data'], 'Thank you for signing' ) ? 'yes' : 'no' )
            . ' shown: '      . ( str_contains ( $gbCurl ['data'], 'marker entry' ) ? 'yes' : 'no' );

?>
