<?php

  padCacheForget ( 'fw-remember' );
  padCacheForget ( 'fw-remember-null' );

  $calls = 0;
  $make  = function () use ( &$calls ) { $calls++; return [ 'made' => $calls ]; };

  $first  = json_encode ( padRemember ( 'fw-remember', 60, $make ) );
  $second = json_encode ( padRemember ( 'fw-remember', 60, $make ) );
  $count  = $calls;

  $nulls = 0;
  $none  = function () use ( &$nulls ) { $nulls++; return NULL; };

  padRemember ( 'fw-remember-null', 60, $none );
  padRemember ( 'fw-remember-null', 60, $none );

  $never = 0;
  $each  = function () use ( &$never ) { return ++$never; };

  padRemember ( 'fw-remember-zero', 0, $each );
  padRemember ( 'fw-remember-zero', 0, $each );

  $forever = padRemember ( 'fw-remember-forever', NULL, function () { return 'for ever'; } );
  $named   = padRemember ( 'fw-remember-named', 60, 'phpversion' ) === phpversion () ? 'yes' : 'no';

?>
