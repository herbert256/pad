<?php
  define ( 'PAD', dirname ( __DIR__, 3 ) . '/pad/' );
  require PAD . 'subs/channels/_lib/notify.php';
  function padMakeSafe ( $s, $n ) { return substr ( $s, 0, $n ); }
  function padCurl ( $request ) { $GLOBALS ['request'] = $request; return $GLOBALS ['reply']; }
  $type = include PAD . 'subs/channels/types/zulip.php';
  $settings = [ 'server' => 'https://chat.example', 'email' => 'bot@example.org', 'key' => 'fixture', 'type' => 'stream', 'to' => 'general', 'topic' => 'Updates' ];
  foreach ( [ 200, 400, 429 ] as $status ) {
    $GLOBALS ['reply'] = [ 'result' => $status, 'data' => json_encode ( [ 'result' => $status === 200 ? 'success' : 'error', 'msg' => 'rejected' ] ) ];
    $result = $type ['send'] ( [ 'text' => 'Hello @**all** & ü' ], $settings );
    if ( ( $result === TRUE ) !== ( $status === 200 ) ) throw new Exception ( 'Status mapping' );
    parse_str ( $GLOBALS ['request'] ['post'], $body );
    if ( $body ['topic'] !== 'Updates' or str_contains ( $body ['content'], '@**all**' ) ) throw new Exception ( 'Native payload' );
  }
  $settings ['type'] = 'direct'; $settings ['to'] = '1,2,1';
  $type ['send'] ( [ 'text' => 'Hello' ], $settings ); parse_str ( $GLOBALS ['request'] ['post'], $body );
  if ( $body ['to'] !== '[1,2]' or isset ( $body ['topic'] ) ) throw new Exception ( 'Direct mapping' );
  if ( $GLOBALS ['request'] ['headers'] ['Authorization'] !== 'Basic ' . base64_encode ( 'bot@example.org:fixture' ) ) throw new Exception ( 'Authentication' );
  unset ( $GLOBALS ['request'] ); $settings ['to'] = 'not-an-id'; $type ['send'] ( [ 'text' => 'x' ], $settings );
  if ( isset ( $GLOBALS ['request'] ) or ! $type ['settings'] ['key'] ['secret'] ) throw new Exception ( 'Invalid ID or credential flag' );
  echo "Zulip: stream/direct payload, authentication, mentions, rejection and throttling passed\n";
?>
