<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  function padError ( $m ) { throw new InvalidArgumentException ( $m ); }
  require PAD . 'lib/subsystem.php';
  require PAD . 'lib/fake.php';
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'fixture failed' ); }
  $rule = include PAD . 'subs/rules/types/ulid.php';
  padFakeSeed ( 7 ); $one = padFake ( 'ulid', [ 'time' => 1700000000000 ] );
  padFakeSeed ( 7 ); check ( $one === padFake ( 'ulid', [ 'time' => 1700000000000 ] ) );
  check ( substr ( $one, 0, 10 ) === '01HF7YAT00' );
  check ( $rule ['check'] ( $one ) );
  check ( substr ( padFake ( 'ulid', [ 'time' => 281474976710655 ] ), 0, 10 ) === '7ZZZZZZZZZ' );
  foreach ( [ NULL, -1, INF, 1.2, 281474976710656 ] as $bad ) {
    try { padFake ( 'ulid', [ 'time' => $bad ] ); throw new Exception ( 'accepted invalid time' ); } catch ( InvalidArgumentException $e ) {}
  }
  echo "fake:ulid fixtures passed\n";
?>
