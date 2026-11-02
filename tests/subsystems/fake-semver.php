<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  function padError ( $m ) { throw new InvalidArgumentException ( $m ); }
  require PAD . 'lib/subsystem.php';
  require PAD . 'lib/fake.php';
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'fixture failed' ); }
  $rule = include PAD . 'subs/rules/types/semver.php';
  padFakeSeed ( 42 ); $one = padFake ( 'semver' );
  padFakeSeed ( 42 ); check ( $one === padFake ( 'semver' ) );
  for ( $i = 0; $i < 500; $i++ ) check ( $rule ['check'] ( padFake ( 'semver', [ 'prerelease' => 'beta.1', 'build' => 'build.00' ] ) ) );
  check ( padFake ( 'semver', [ 'major' => 1, 'minor' => 2, 'patch' => 3 ] ) === '1.2.3' );
  foreach ( [ [ 'major' => -1 ], [ 'minor' => [ 3, 1 ] ], [ 'prerelease' => '01' ], [ 'build' => 'x.' ] ] as $bad ) {
    try { padFake ( 'semver', $bad ); throw new Exception ( 'accepted invalid semver option' ); } catch ( InvalidArgumentException $e ) {}
  }
  echo "fake:semver fixtures passed\n";
?>
