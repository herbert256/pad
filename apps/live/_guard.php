<?php

  // The live application runs the code it is given, as a page of this server: a local
  // request only - the command line, or this machine asking itself with nothing forwarded
  // (padLocal) - that names this machine in its Host header. A page on another site that
  // has its own name resolve to 127.0.0.1 (DNS rebinding) reaches the loopback address too,
  // but it names its own site as the host. The pages saved here are behind the same guard.

  $liveHost = strtolower ( preg_replace ( '/:\d+$/', '', trim ( (string) ( $_SERVER ['HTTP_HOST'] ?? '' ) ) ) );

  if ( ! padLocal () or ( PHP_SAPI !== 'cli' and ! in_array ( $liveHost, [ 'localhost', '127.0.0.1', '[::1]' ], TRUE ) ) )
    padAbort ( 403, 'The PAD live application runs the code it is given, so it answers this machine only.' );

  unset ( $liveHost );

  return TRUE;

?>
