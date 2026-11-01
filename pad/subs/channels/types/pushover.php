<?php

  // pushover - a push to the phones and desktops of a Pushover user, or of every member of a
  // Pushover group, through its Message API (https://pushover.net/api): the application's
  // token in PUSHOVER_TOKEN, the user's or group's key in the recipient's field pushover or
  // PUSHOVER_USER - each 30 letters and digits. The title goes as the title, cut at its 250
  // characters, the @pushover@ section, or the plain text, as the message, cut at its 1024,
  // as plain text - no html=1, so a value is never read as markup - and a url of the message
  // as the url, left out past its 512. The options priority -2, -1, 0 or 1 (silent, quiet,
  // normal, high: 2, an emergency the person must acknowledge, comes with receipts and
  // repeats this channel does not follow), sound (a sound of Pushover's or the user's own,
  // by its name) and ttl (the seconds before the devices drop it). Sent form-encoded, as the
  // API's own example is, so the reports redact the token by its field's name; an answer of
  // 4xx says what was wrong in its errors.

  return [
    'settings' => [ 'token'    => [ 'env' => 'PUSHOVER_TOKEN', 'pattern' => '/^[A-Za-z0-9]{30}$/D', 'secret' => TRUE,
                                    'is' => 'Pushover application token - 30 letters and digits' ],
                    'user'     => [ 'env' => 'PUSHOVER_USER', 'to' => 'pushover', 'pattern' => '/^[A-Za-z0-9]{30}$/D',
                                    'is' => 'Pushover user or group key - 30 letters and digits' ],
                    'priority' => [ 'default' => '', 'pattern' => '/^(-2|-1|0|1)$/D',
                                    'is' => 'Pushover priority -2, -1, 0 or 1 - an emergency, 2, needs receipts this channel does not follow' ],
                    'sound'    => [ 'default' => '', 'pattern' => '/^[A-Za-z0-9_-]{1,64}$/D', 'is' => 'Pushover sound - its name' ],
                    'ttl'      => [ 'default' => '', 'pattern' => '/^[1-9][0-9]{0,8}$/D',    'is' => 'number of seconds' ] ],
    'send'     => function ( $message, $settings ) {
      $body = [ 'token' => (string) $settings ['token'], 'user' => (string) $settings ['user'], 'message' => padNotifyCut ( $message ['text'], 1024 ) ];
      if ( $message ['title'] !== '' )
        $body ['title'] = padNotifyCut ( $message ['title'], 250 );
      if ( $message ['url'] !== '' and mb_strlen ( $message ['url'], 'UTF-8' ) <= 512 )
        $body ['url'] = $message ['url'];
      foreach ( [ 'priority', 'sound', 'ttl' ] as $option )
        if ( (string) $settings [$option] !== '' )
          $body [$option] = (string) $settings [$option];
      return padNotifyAnswer ( padNotifyHttp ( 'POST', 'https://api.pushover.net/1/messages.json', http_build_query ( $body ),
                                               [ 'Content-Type' => 'application/x-www-form-urlencoded' ] ), 'Pushover' );
    },
  ];

?>
