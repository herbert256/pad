<?php

  // In the browser a producer that answers FALSE with nothing sent is answered 204, which
  // tells EventSource not to come back - on a server the stream just ends, answered 200.

  $_SERVER ['PAD_WASM'] = '1';

  padSse ( fn () => FALSE, [ 'every' => 1 ] );

?>
