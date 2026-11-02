<?php
  return [
    'settings' => [
      'server' => [ 'env' => 'ZULIP_URL', 'address' => TRUE ],
      'email' => [ 'env' => 'ZULIP_EMAIL', 'pattern' => '/^[^\s:@]+@[^\s:@]+$/D', 'secret' => TRUE ],
      'key' => [ 'env' => 'ZULIP_API_KEY', 'secret' => TRUE, 'pattern' => '/^[^\s:]+$/D' ],
      'type' => [ 'default' => 'stream', 'pattern' => '/^(stream|direct)$/D' ],
      'to' => [ 'env' => 'ZULIP_TO', 'to' => 'zulip', 'pattern' => '/^[^\r\n]{1,200}$/uD' ],
      'topic' => [ 'env' => 'ZULIP_TOPIC', 'default' => 'Notifications', 'pattern' => '/^[^\r\n]{1,60}$/uD' ],
    ],
    'send' => function ( $message, $settings ) {
      $server = rtrim ( $settings ['server'], '/' );
      if ( ! preg_match ( '~^https://[A-Za-z0-9.-]+(?::[0-9]{1,5})?$~D', $server ) ) return 'Zulip requires an https server origin';
      $to = (string) $settings ['to'];
      if ( $settings ['type'] === 'direct' ) {
        if ( ! preg_match ( '/^[1-9]\d*(?:,[1-9]\d*)*$/D', $to ) ) return 'Zulip direct recipients must be comma-separated positive user IDs';
        $ids = explode ( ',', $to );
        if ( count ( $ids ) > 100 or array_filter ( $ids, fn ( $id ) => strlen ( $id ) > 15 ) ) return 'Zulip direct recipients exceed limits';
        $to = json_encode ( array_values ( array_unique ( array_map ( 'intval', $ids ) ) ) );
      }
      $text = str_replace ( [ '@**', '@_**' ], [ "@\u{200B}**", "@\u{200B}_**" ], $message ['text'] );
      if ( mb_strlen ( $text, 'UTF-8' ) > 10000 ) return 'Zulip text exceeds 10000 characters';
      $body = [ 'type' => $settings ['type'], 'to' => $to, 'content' => $text ];
      if ( $settings ['type'] === 'stream' ) $body ['topic'] = $settings ['topic'];
      $answer = padNotifyHttp ( 'POST', "$server/api/v1/messages", http_build_query ( $body, '', '&', PHP_QUERY_RFC3986 ),
        [ 'Authorization' => 'Basic ' . base64_encode ( $settings ['email'] . ':' . $settings ['key'] ), 'Content-Type' => 'application/x-www-form-urlencoded' ] );
      if ( ( $answer ['json'] ['result'] ?? '' ) === 'success' and $answer ['status'] >= 200 and $answer ['status'] < 300 ) return TRUE;
      $answer ['json'] ['message'] = $answer ['json'] ['msg'] ?? 'The server did not confirm delivery';
      if ( $answer ['status'] >= 200 and $answer ['status'] < 300 ) return 'Zulip: ' . padMakeSafe ( $answer ['json'] ['message'], 160 );
      return padNotifyAnswer ( $answer, 'Zulip' );
    },
  ];
?>
