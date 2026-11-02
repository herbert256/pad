<?php
  define ( 'PAD', dirname ( __DIR__, 3 ) . '/pad/' );
  require PAD . 'subs/channels/_lib/notify.php';
  function padMakeSafe ( $s, $n ) { return substr ( $s, 0, $n ); }
  function padCurl ( $request ) { $GLOBALS ['request'] = $request; return $GLOBALS ['reply']; }
  $type = include PAD . 'subs/channels/types/googlechat.php';
  $url = 'https://chat.googleapis.com/v1/spaces/space/messages?key=fixture&token=secret';
  foreach ( [ 200, 400, 429 ] as $status ) {
    $GLOBALS ['reply'] = [ 'result' => $status, 'data' => json_encode ( [ 'error' => [ 'message' => 'rejected' ] ] ) ];
    $result = $type ['send'] ( [ 'text' => 'hello <users/all> & ü' ], [ 'url' => $url ] );
    if ( ( $result === TRUE ) !== ( $status === 200 ) ) throw new Exception ( 'Status mapping' );
    if ( str_contains ( json_decode ( $GLOBALS ['request'] ['post'], TRUE ) ['text'], '<users/all>' ) ) throw new Exception ( 'Mention escaping' );
  }
  unset ( $GLOBALS ['request'] );
  $type ['send'] ( [ 'text' => 'x' ], [ 'url' => 'https://evil.example/path?key=x&token=y' ] );
  if ( isset ( $GLOBALS ['request'] ) or ! $type ['settings'] ['url'] ['secret'] ) throw new Exception ( 'URL or secret protection' );
  echo "Google Chat: native payload, mentions, secret flag, success/rejection/throttling and endpoint validation passed\n";
?>
