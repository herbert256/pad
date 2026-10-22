<?php

  // A settings form posting PHP's bracket names - api[key], stripe[key], smtp[password],
  // payment[card][number] - or a JSON body of that shape: each field is judged with the names
  // of its groups before it, as api_key and smtp_password, and a key under a named group is
  // that group's credential. The SMTP host, the card's expiry, a setting, a list row's
  // key/value pair and the e-mail address come back.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'api' => [ 'key' => 'sk1' ], 'stripe' => [ 'key' => 'sk2' ], 'license' => [ 'key' => 'AAAA' ],
             'mfa' => [ 'code' => '123456' ], 'recovery' => [ 'code' => 'r1' ], 'security' => [ 'answer' => 'Rex' ],
             'smtp' => [ 'password' => 'p1', 'host' => 'mail' ],
             'settings' => [ 'api' => [ 'key' => 'k1' ], 'otp_enabled' => '1' ],
             'payment' => [ 'card' => [ 'number' => '4111', 'expiry' => '12-30' ] ],
             'fields' => [ [ 'key' => 'color', 'value' => 'red' ] ], 'user' => [ 'email' => 'ann@example.com' ] ];

  padFlashInput ();

  $flashed = [];

  foreach ( [ 'api.key', 'stripe.key', 'license.key', 'mfa.code', 'recovery.code', 'security.answer', 'smtp.password',
              'smtp.host', 'settings.api.key', 'settings.otp_enabled', 'payment.card.number', 'payment.card.expiry',
              'fields.0.key', 'fields.0.value', 'user.email' ] as $flashKey )
    $flashed [] = "$flashKey=[" . padOld ( $flashKey ) . ']';

  $flashed = implode ( ' ', $flashed );

?>
