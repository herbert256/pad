<?php

  // A whole action round-trip against the demo todo application, on disposable data: the
  // store is snapshotted, a marker task is posted through the real todoPost action, the
  // response must show it, and the snapshot is written back - the demo answers afterwards
  // exactly as it did before.

  $todoStore    = DATA . 'demo/todos.json';
  $todoSnapshot = padFileGet ( $todoStore, '[]' );

  $curl = padCurl ( [ 'url' => $padHost . 'demo/?todoPost',
                      'post' => [ 'go' => 'add', 'task' => 'regression marker task' ] ] );

  $added = str_contains ( $curl ['data'], 'regression marker task' );

  padFilePut ( $todoStore, $todoSnapshot );

  $after = padCurl ( $padHost . 'demo/?todo&padInclude' );

  $todoResult = $curl ['result'] . ' added: ' . ( $added ? 'yes' : 'no' )
              . ' restored: ' . ( str_contains ( $after ['data'], 'regression marker task' ) ? 'no' : 'yes' );

?>