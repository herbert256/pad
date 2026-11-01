<?php

  // matrix - a message in a Matrix room, sent by a bot account: the homeserver in
  // MATRIX_HOMESERVER (https://matrix.org), the account's access token in MATRIX_TOKEN, the
  // room - its id, !abc:matrix.org - in the recipient's field matrix or MATRIX_ROOM. The
  // @matrix@ section, or the plain text, is an m.text message, put with a transaction id of
  // its own, random, as the client-server API asks for every message.

  return [
    'settings' => [ 'server' => [ 'env' => 'MATRIX_HOMESERVER', 'default' => 'https://matrix.org' ],
                    'token'  => [ 'env' => 'MATRIX_TOKEN' ],
                    'room'   => [ 'env' => 'MATRIX_ROOM', 'to' => 'matrix' ] ],
    'send'     => function ( $message, $settings ) {
      $txn = 'pad' . bin2hex ( random_bytes ( 8 ) );
      $url = rtrim ( (string) $settings ['server'], '/' ) . '/_matrix/client/v3/rooms/' . rawurlencode ( (string) $settings ['room'] )
           . "/send/m.room.message/$txn";
      return padNotifyAnswer ( padNotifyHttp ( 'PUT', $url, [ 'msgtype' => 'm.text', 'body' => $message ['text'] ],
                                               [ 'Authorization' => 'Bearer ' . $settings ['token'] ] ), 'Matrix' );
    },
  ];

?>
