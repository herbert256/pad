<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  function padError ( $m ) { throw new InvalidArgumentException ( $m ); }
  require PAD . 'lib/subsystem.php';
  require PAD . 'lib/fake.php';
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'fixture failed' ); }
  padFakeSeed ( 9 ); $samples = [];
  for ( $i = 0; $i < 10000; $i++ ) $samples [] = padFake ( 'weighted', [ 'values' => [ 0, 1, 2 ], 'weights' => [ 0, 1, 9 ] ] );
  check ( ! in_array ( 0, $samples, TRUE ) );
  $n = count ( array_filter ( $samples, fn ( $v ) => $v === 1 ) ); check ( $n > 850 and $n < 1150 );
  padFakeSeed ( 9 ); check ( $samples [0] === padFake ( 'weighted', [ 'values' => [ 0, 1, 2 ], 'weights' => [ 0, 1, 9 ] ] ) );
  check ( padFake ( 'weighted', [ 'values' => [ [ 'x' => 1 ] ], 'weights' => [ 1e308 ] ] ) === [ 'x' => 1 ] );
  foreach ( [ [0,0], [-1,1], [INF,1] ] as $bad ) {
    try { padFake ( 'weighted', [ 'values' => [1,2], 'weights' => $bad ] ); throw new Exception ( 'accepted invalid weight' ); } catch ( InvalidArgumentException $e ) {}
  }
  echo "fake:weighted fixtures passed\n";
?>
