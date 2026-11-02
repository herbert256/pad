<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  require PAD . 'subs/channels/_lib/notify.php';
  function padMakeSafe ( $s, $n ) { return substr ( $s, 0, $n ); }
  function padCurl ( $request ) { $GLOBALS ['request'] = $request; return $GLOBALS ['reply']; }
  $type = include PAD . 'subs/channels/types/zulip.php';
  $settings = [ 'server' => 'https://chat.example', 'email' => 'bot@example.org', 'key' => 'fixture', 'type' => 'stream', 'to' => 'general', 'topic' => 'Updates' ];
  foreach ( [ 200, 400, 429 ] as $status ) {
    $GLOBALS ['reply'] = [ 'result' => $status, 'data' => json_encode ( [ 'result' => $status === 200 ? 'success' : 'error', 'msg' => 'rejected' ] ) ];
    $result = $type ['send'] ( [ 'text' => 'Hello @**all** @**Jane** @_**Jane** @*support* @_*support* & ü' ], $settings );
    if ( ( $result === TRUE ) !== ( $status === 200 ) ) throw new Exception ( 'Status mapping' );
    parse_str ( $GLOBALS ['request'] ['post'], $body );
    if ( $body ['topic'] !== 'Updates' or preg_match ( '/@_?\*/', $body ['content'] ) or ! str_contains ( $body ['content'], '& ü' ) ) throw new Exception ( 'Native payload and mention neutralization' );
  }
  $GLOBALS ['reply'] = [ 'result' => 200, 'data' => '{"result":"success"}' ];
  foreach ( [ 'general', '123', 'true', 'null', '["general"]', '"quoted"', 'R&D ü' ] as $channel ) {
    $settings ['to'] = $channel;
    $type ['send'] ( [ 'text' => 'Hello' ], $settings ); parse_str ( $GLOBALS ['request'] ['post'], $body );
    if ( json_decode ( $body ['to'], TRUE, 512, JSON_THROW_ON_ERROR ) !== $channel ) throw new Exception ( 'Named channel interpreted as a different JSON type' );
  }
  $settings ['type'] = 'direct'; $settings ['to'] = '1,2,1';
  $type ['send'] ( [ 'text' => 'Hello' ], $settings ); parse_str ( $GLOBALS ['request'] ['post'], $body );
  if ( $body ['to'] !== '[1,2]' or isset ( $body ['topic'] ) ) throw new Exception ( 'Direct mapping' );
  if ( $GLOBALS ['request'] ['headers'] ['Authorization'] !== 'Basic ' . base64_encode ( 'bot@example.org:fixture' ) ) throw new Exception ( 'Authentication' );
  unset ( $GLOBALS ['request'] ); $settings ['to'] = 'not-an-id'; $type ['send'] ( [ 'text' => 'x' ], $settings );
  if ( isset ( $GLOBALS ['request'] ) or ! $type ['settings'] ['key'] ['secret'] ) throw new Exception ( 'Invalid ID or credential flag' );
  echo "Zulip: string channel names, direct IDs, authentication, user/group/wildcard mentions, rejection and throttling passed\n";
?>
