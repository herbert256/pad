<?php

  // padTap answers the value whatever the callback answers; the callback saw it, and an
  // object it changed is answered changed.

  $seen = [];

  $list   = padTap ( [ 1, 2, 3 ], function ( $v ) use ( &$seen ) { $seen [] = count ( $v ); return 'ignored'; } );
  $text   = padTap ( 'abc', function ( $v ) use ( &$seen ) { $seen [] = $v; } );
  $none   = padTap ( NULL, function ( $v ) use ( &$seen ) { $seen [] = $v; } );
  $object = padTap ( new stdClass, function ( $o ) { $o->name = 'set in passing'; } );

  $r = json_encode ( [ $list, $text, $none, $object, $seen ] );

?>
