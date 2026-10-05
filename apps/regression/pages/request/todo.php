<?php

  // A whole action round-trip against the demo todo application, on disposable data: the
  // store is snapshotted, a marker task is posted through the real todoPost action, the
  // response must show it, and the snapshot is written back - the demo answers afterwards
  // exactly as it did before. The demo checks CSRF tokens, so the token of the page's form
  // goes with the post, together with the session cookie it belongs to.

  $todoStore    = DATA . 'demo/todos.json';
  $todoSnapshot = padFileGet ( $todoStore, '[]' );

  $form  = padCurl ( $padHost . 'demo/?todo&padInclude' );
  $token = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $form ['data'], $match ) ? $match [1] : '';

  $curl = padCurl ( [ 'url'     => $padHost . 'demo/?todoPost',
                      'cookies' => [ 'PHPSESSID' => $form ['cookies'] ['PHPSESSID'] ?? '' ],
                      'post'    => [ 'go' => 'add', 'task' => 'regression marker task', 'padCsrfToken' => $token ] ] );

  $added = str_contains ( $curl ['data'], 'regression marker task' );

  padFilePut ( $todoStore, $todoSnapshot );

  $after = padCurl ( $padHost . 'demo/?todo&padInclude' );

  $todoResult = $curl ['result'] . ' added: ' . ( $added ? 'yes' : 'no' )
              . ' restored: ' . ( str_contains ( $after ['data'], 'regression marker task' ) ? 'no' : 'yes' );

?>