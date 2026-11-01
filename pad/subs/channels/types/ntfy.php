<?php

  // ntfy - a push to the phones and desktops subscribed to an ntfy topic: the topic in the
  // recipient's field ntfy or NTFY_TOPIC - letters, digits, _ and - -, the server in NTFY_URL
  // (https://ntfy.sh unless given; never the recipient's), an access token in NTFY_TOKEN when
  // the topic is protected. The title goes as the Title header, the @ntfy@ section, or the
  // plain text, as the body; a url of the message as the Click header.

  return [
    'settings' => [ 'topic'  => [ 'env' => 'NTFY_TOPIC', 'to' => 'ntfy', 'pattern' => '/^[A-Za-z0-9_-]{1,64}$/D',
                                  'is' => 'ntfy topic - letters, digits, _ and -' ],
                    'server' => [ 'env' => 'NTFY_URL',   'default' => 'https://ntfy.sh', 'address' => TRUE ],
                    'token'  => [ 'env' => 'NTFY_TOKEN', 'default' => '' ] ],
    'send'     => function ( $message, $settings ) {
      $headers = [ 'Title' => str_replace ( [ "\r", "\n" ], ' ', $message ['title'] ) ];
      if ( $message ['url'] !== '' )
        $headers ['Click'] = str_replace ( [ "\r", "\n" ], '', $message ['url'] );
      if ( $settings ['token'] !== '' )
        $headers ['Authorization'] = 'Bearer ' . $settings ['token'];
      return padNotifyAnswer ( padNotifyHttp ( 'POST', rtrim ( (string) $settings ['server'], '/' ) . '/' . $settings ['topic'], $message ['text'],
                                               $headers + [ 'Content-Type' => 'text/plain; charset=utf-8' ] ), 'ntfy' );
    },
  ];

?>
