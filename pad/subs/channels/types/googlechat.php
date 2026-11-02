<?php
  return [
    'settings' => [ 'url' => [ 'env' => 'GOOGLECHAT_WEBHOOK_URL', 'address' => TRUE, 'secret' => TRUE ] ],
    'send' => function ( $message, $settings ) {
      $url = $settings ['url']; $parts = parse_url ( $url );
      if ( ! $parts or ( $parts ['scheme'] ?? '' ) !== 'https' or ( $parts ['host'] ?? '' ) !== 'chat.googleapis.com'
           or isset ( $parts ['user'] ) or isset ( $parts ['port'] ) or isset ( $parts ['fragment'] )
           or ! preg_match ( '~^/v1/spaces/[A-Za-z0-9_-]+/messages$~D', $parts ['path'] ?? '' ) ) return 'Google Chat requires a configured incoming webhook URL';
      parse_str ( $parts ['query'] ?? '', $query );
      foreach ( [ 'key', 'token' ] as $key )
        if ( ! is_string ( $query [$key] ?? NULL ) or $query [$key] === '' ) return 'Google Chat webhook is missing its key or token';
      // Provider mention syntax is disabled in notification data; visible text is retained.
      $text = str_replace ( '<users/', "<\u{200B}users/", $message ['text'] );
      if ( strlen ( $text ) > 30000 ) return 'Google Chat text exceeds 30000 bytes';
      $answer = padNotifyHttp ( 'POST', $url, [ 'text' => $text ] );
      if ( isset ( $answer ['json'] ['error'] ['message'] ) ) $answer ['json'] ['message'] = $answer ['json'] ['error'] ['message'];
      return padNotifyAnswer ( $answer, 'Google Chat' );
    },
  ];
?>
