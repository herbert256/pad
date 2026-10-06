<?php

  // The demo's contact form posted without a mistake: its rules stand on the fields of
  // contact.pad and are checked before contact.php runs, which then stores the message and
  // sends the browser on to a plain GET of the page, thanked by a flash message. The store
  // is put back as it was - removed again when there was none.

  $cvStore    = DATA . 'demo/messages.json';
  $cvSnapshot = file_exists ( $cvStore ) ? padFileGet ( $cvStore ) : NULL;

  $cvForm  = padCurl ( $padHost . 'demo/?contact&padInclude' );
  $cvToken = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $cvForm ['data'], $cvMatch ) ? $cvMatch [1] : '';
  $cvJar   = [ 'PHPSESSID' => $cvForm ['cookies'] ['PHPSESSID'] ?? '' ];

  $cvPost = padCurl ( [ 'url'     => $padHost . 'demo/?contact',
                        'cookies' => $cvJar,
                        'options' => [ 'FOLLOWLOCATION' => FALSE ],
                        'post'    => [ 'padCsrfToken' => $cvToken, 'padForm' => 'contact',
                                       'name' => 'regression', 'email' => 'regression@example.com',
                                       'subject' => 'marker subject', 'message' => 'marker message' ] ] );

  $cvStored = str_contains ( (string) @file_get_contents ( $cvStore ), 'marker message' );

  $cvJar ['padFlash'] = $cvPost ['cookies'] ['padFlash'] ?? '';

  $cvCurl = padCurl ( [ 'url' => $cvPost ['info'] ['redirect_url'] ?? '', 'cookies' => $cvJar ] );

  if ( $cvSnapshot === NULL )
    @unlink ( $cvStore );
  else
    padFilePut ( $cvStore, $cvSnapshot );

  $contactValidResult = $cvPost ['result']
                      . ' redirected: ' . ( str_ends_with ( $cvPost ['info'] ['redirect_url'] ?? '', 'demo/?contact' ) ? 'yes' : 'no' )
                      . ' stored: '     . ( $cvStored ? 'yes' : 'no' )
                      . ' thanked: '    . ( str_contains ( $cvCurl ['data'], 'Thank you for your message' ) ? 'yes' : 'no' )
                      . ' emptied: '    . ( str_contains ( $cvCurl ['data'], 'marker' ) ? 'no' : 'yes' );

?>
