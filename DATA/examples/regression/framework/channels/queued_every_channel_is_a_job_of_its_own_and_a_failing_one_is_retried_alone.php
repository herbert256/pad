<?php

  // Queued, a notification is a job per channel ('@notify'): the request answers at once, the
  // worker renders and sends - here padQueueWork in the same request, the fakes still on. Slack
  // answers 500 the first time: its job comes back for a retry, the mail that went is not
  // sent again; the retry delivers it.

  padQueueFlush ( 'notifytest' );

  $slack = 0;

  padCurlFake ( [ 'https://hooks.slack.com/*' => function () use ( &$slack ) {
    return ++$slack == 1 ? [ 'status' => 500, 'data' => 'rollup_error' ] : 'ok';
  } ] );

  $queued = padNotify ( [ 'id' => 9, 'email' => 'cy@example.com' ], 'shipped', [ 'order' => 77, 'items' => 1, 'name' => 'Cy', 'url' => '' ],
                        [ 'mail', 'slack' ], [ 'queue' => 'notifytest', 'slack' => [ 'url' => 'https://hooks.slack.com/services/T/B/q' ] ] );

  $jobs   = array_map ( fn ( $one ) => is_string ( $one ['job'] ) ? 'a job' : 'no job', padNotifyResults () );
  $size   = padQueueSize ( 'notifytest' );
  $first  = padQueueWork ( 'notifytest', 0, 60, 3 );
  $mailed = $padMailLast ['subject'] ?? '';

  $padMailLast = NULL;

  // The retry is due after the backoff: the job file is made due now.

  foreach ( glob ( DATA . 'queue/regression/framework/notifytest/*.job' ) as $file ) {
    $record = json_decode ( file_get_contents ( $file ), TRUE );
    $record ['available'] = time () - 1;
    file_put_contents ( $file, json_encode ( $record ) );
    rename ( $file, dirname ( $file ) . '/' . sprintf ( '%010d', time () - 1 ) . '-' . $record ['id'] . '.job' );
  }

  $second = padQueueWork ( 'notifytest', 0, 60, 3 );

  $report = json_encode ( [ 'queued' => $queued, 'jobs' => $jobs, 'waiting' => $size,
                            'first'  => [ $first ['done'], $first ['retried'], $first ['failed'] ], 'mailed' => $mailed,
                            'second' => [ $second ['done'], $second ['retried'], $second ['failed'] ], 'mailed again' => $padMailLast !== NULL,
                            'slack asked' => $slack ], JSON_UNESCAPED_SLASHES );

?>
