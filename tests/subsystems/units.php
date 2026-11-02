<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $path ) { return scandir ( $path ); }
  function padError ( $message ) { throw new InvalidArgumentException ( $message ); }
  require PAD . 'lib/subsystem.php';
  require PAD . 'lib/units.php';
  function check ( $yes ) { if ( ! $yes ) throw new Exception ( 'unit fixture failed' ); }
  check ( abs ( padConvert ( 100, 'cm', 'm' ) - 1 ) < 1e-12 );
  check ( abs ( padConvert ( 32, 'F', 'C' ) ) < 1e-12 );
  check ( abs ( padConvert ( 212, 'F', 'C' ) - 100 ) < 1e-12 );
  check ( padConvert ( 1, 'KiB', 'B' ) == 1024 );
  check ( padConvert ( 1, 'kB', 'B' ) == 1000 );
  check ( abs ( padConvert ( 1, 'acre', 'm2' ) - 4046.8564224 ) < 1e-9 );
  foreach ( padUnits () as $family => $definition )
    foreach ( $definition ['units'] as $symbol => $unit ) {
      $value = $family === 'temperature' ? 300 : 12.5;
      check ( abs ( padConvert ( padConvert ( $value, $symbol, $definition ['canonical'] ), $definition ['canonical'], $symbol ) - $value ) < 1e-8 );
    }
  foreach ( [ [ 1, 'm', 'kg' ], [ INF, 'm', 'cm' ], [ 1, 'gal', 'L' ], [ -1, 'K', 'C' ] ] as $args ) {
    try { padConvert ( ...$args ); throw new Exception ( 'invalid conversion accepted' ); }
    catch ( InvalidArgumentException $e ) {}
  }
  echo "units: known values, every round trip and invalid inputs passed\n";
?>
