<?php

  // padOnce runs its callback the first time its line is reached and answers that result
  // from then on - in a loop, and from a function called again; another line is another
  // place. A callback that throws gave no result, so the place runs it again.

  $runs = 0;

  $loop = [];

  foreach ( [ 1, 2, 3 ] as $round )
    $loop [] = padOnce ( function () use ( &$runs, $round ) { $runs++; return "made in round $round"; } );

  $settings = function () {
    return padOnce ( fn () => [ 'loaded' => microtime ( TRUE ) ] );
  };

  $same = ( $settings () === $settings () ) ? 'the same settings' : 'loaded again';

  $other = padOnce ( function () use ( &$runs ) { $runs++; return 'another place'; } );

  $tries = 0;

  for ( $i = 0 ; $i < 3 ; $i++ )
    try {
      $made = padOnce ( function () use ( &$tries ) { if ( ++$tries < 2 ) throw new RuntimeException; return "made on try $tries"; } );
    } catch ( RuntimeException $e ) {
      $made = 'threw';
    }

  $r = json_encode ( [ $loop, $runs, $same, $other, $made, $tries ] );

?>
