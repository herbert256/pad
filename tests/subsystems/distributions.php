<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  function padError ( $m ) { throw new InvalidArgumentException ( $m ); }
  require PAD . 'lib/subsystem.php'; require PAD . 'lib/fake.php'; require PAD . 'lib/distributions.php';
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'sampling fixture failed' ); }
  foreach ( [ 'uniform' => .5, 'normal' => 0, 'bernoulli' => .5, 'binomial' => .5, 'poisson' => 1, 'exponential' => 1 ] as $kind => $mean ) {
    $samples = padSample ( $kind, 10000, [ 'seed' => 7 ] );
    check ( abs ( array_sum ( $samples ) / count ( $samples ) - $mean ) < .04 );
    check ( array_slice ( $samples, 0, 10 ) === padSample ( $kind, 10, [ 'seed' => 7 ] ) );
  }
  check ( padSample ( 'uniform', 1, [ 'random' => fn () => .25, 'min' => 2, 'max' => 6 ] ) === [ 3.0 ] );
  check ( padSample ( 'binomial', 3, [ 'n' => 10, 'p' => 1, 'seed' => 2 ] ) === [ 10, 10, 10 ] );
  $many = padSample ( 'poisson', 1000, [ 'lambda' => 1000, 'seed' => 19 ] );
  check ( abs ( array_sum ( $many ) / 1000 - 1000 ) < 4 );
  padFakeSeed ( 20 ); $before = padFakeState (); padSample ( 'normal', 50, [ 'seed' => 7 ] ); check ( padFakeState () === $before );
  foreach ( [ ['normal',['sd'=>0]], ['poisson',['lambda'=>-1]], ['uniform',['min'=>2,'max'=>1]], ['binomial',['n'=>.2]], ['exponential',['rate'=>INF]], ['uniform',['random'=>fn()=>1]], ['poisson',['random'=>fn()=>0]] ] as [$kind,$options] ) {
    try { padSample ( $kind, 1, $options ); throw new Exception ( 'accepted invalid parameters or stream' ); } catch ( InvalidArgumentException $e ) {}
  }
  echo "distributions: repeatability, fixed-seed means, supports, stream restoration and errors passed\n";
?>
