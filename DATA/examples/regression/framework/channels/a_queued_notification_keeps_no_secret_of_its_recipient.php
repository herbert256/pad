<?php

  // A queued notification is a job file under DATA/queue/ - kept with the failed jobs after
  // its tries, listed by the admin console - and its recipient goes along for the worker. Of
  // a user row it keeps what padLogin keeps: the password hash, the remember token, the
  // second factor's secret are left out; the fields the channels and the template read - the
  // id, the email, a push subscription with its keys - stay.

  padQueueFlush ( 'notifysecret' );

  padNotify ( [ 'id' => 'r26f', 'name' => 'Ann', 'email' => 'ann@example.com', 'password_hash' => '$2y$12$abcdefghijklmnopqrstuv',
                'remember_token' => 'rt_9f8e7d', 'totp_secret' => 'JBSWY3DPEHPK3PXP', 'api_key' => 'ak_123',
                'webpush' => [ 'endpoint' => 'https://fcm.googleapis.com/fcm/send/x', 'keys' => [ 'p256dh' => 'BP', 'auth' => 'au' ] ] ],
              'shipped', [ 'order' => 42, 'items' => 1, 'name' => 'Ann', 'url' => '' ], [ 'mail' ], [ 'queue' => 'notifysecret' ] );

  $kept = [];

  foreach ( glob ( DATA . 'queue/regression/framework/notifysecret/*.job' ) as $file )
    $kept = json_decode ( file_get_contents ( $file ), TRUE ) ['data'] ['to'] ?? [];

  padQueueFlush ( 'notifysecret' );

  $json = json_encode ( $kept, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
