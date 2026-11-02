<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  function padError ( $m ) { throw new InvalidArgumentException ( $m ); }
  require PAD . 'lib/subsystem.php'; require PAD . 'lib/units.php'; require PAD . 'lib/geospatial.php';
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'geospatial fixture failed' ); }
  check ( abs ( padGeo ( 'distance', [0,0], [90,0], ['unit'=>'km'] ) - M_PI * 6371.0088 / 2 ) < 1e-9 );
  check ( abs ( padGeo ( 'distance', [179,0], [-179,0] ) - 222390.160467 ) < .001 );
  check ( padGeo ( 'distance', [0,90], [120,90] ) < 1e-8 );
  check ( abs ( padGeo ( 'bearing', [0,0], [1,0] ) - 90 ) < 1e-9 );
  check ( padGeo ( 'bbox', [[179,1],[-179,4]] ) === [179.0,1.0,-179.0,4.0] );
  $polygon = ['type'=>'Polygon','coordinates'=>[[[170,-10],[-170,-10],[-170,10],[170,10],[170,-10]],[[175,-2],[-175,-2],[-175,2],[175,2],[175,-2]]]];
  check ( padGeo ( 'contains', [179,5], $polygon ) );
  check ( ! padGeo ( 'contains', [179,0], $polygon ) );
  check ( ! padGeo ( 'contains', [0,0], $polygon ) );
  check ( padGeo ( 'contains', [170,0], $polygon ) );
  check ( ! padGeo ( 'contains', [170,0], $polygon, ['boundary'=>false] ) );
  check ( padGeo ( 'geohash', [-5.6,42.6], NULL, ['precision'=>5] ) === 'ezs42' );
  $decoded = padGeo ( 'geohash', 'ezs42' ); check ( abs ( $decoded ['point'][0] + 5.6 ) < .03 );
  foreach ( [ ['distance',[181,0],[0,0]], ['bearing',[0,0],[180,0]], ['geohash','ABC',NULL], ['contains',[0,0],['type'=>'Polygon','coordinates'=>[[[0,0],[2,2],[0,2],[2,0],[0,0]]]]] ] as $args ) {
    try { padGeo ( ...$args ); throw new Exception ( 'invalid geometry accepted' ); } catch ( InvalidArgumentException $e ) {}
  }
  echo "geospatial: distances, poles, dateline, holes, boundaries, geohash and invalid geometry passed\n";
?>
