<?php

  // padWasm is TRUE where the runtime of a packed application passes PAD_WASM in $_SERVER -
  // never from a request header, which arrives as HTTP_PAD_WASM - and empty is not set.

  $answers = [];

  $answers [] = padWasm () ? 'yes' : 'no';

  $_SERVER ['HTTP_PAD_WASM'] = '1';
  $answers [] = padWasm () ? 'yes' : 'no';

  $_SERVER ['PAD_WASM'] = '';
  $answers [] = padWasm () ? 'yes' : 'no';

  $_SERVER ['PAD_WASM'] = '1';
  $answers [] = padWasm () ? 'yes' : 'no';

  unset ( $_SERVER ['PAD_WASM'], $_SERVER ['HTTP_PAD_WASM'] );

  $answers = implode ( ' ', $answers );

?>
