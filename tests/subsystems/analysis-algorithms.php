<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $path ) { return scandir ( $path ); }
  function padError ( $message ) { throw new InvalidArgumentException ( $message ); }
  require PAD . 'lib/subsystem.php';
  foreach ( [ 'statistics', 'graphs', 'schemas', 'textanalysis', 'constraints', 'units', 'geospatial', 'fake', 'distributions' ] as $library ) require PAD . "lib/$library.php";
  $checks = 0;
  function check ( $condition, $message = 'algorithm fixture failed' ) { global $checks; $checks++; if ( ! $condition ) throw new RuntimeException ( $message ); }
  function near ( $actual, $expected, $epsilon = 1e-10 ) { check ( abs ( $actual - $expected ) <= $epsilon * max ( 1, abs ( $expected ) ), "$actual differs from $expected" ); }
  function rejects ( $call ) { try { $call (); } catch ( InvalidArgumentException $e ) { check ( TRUE ); return; } throw new RuntimeException ( 'invalid input accepted' ); }

  $stats = padStats ( 'describe', [ 1, 2, 3, 4, NULL ] );
  near ( $stats ['mean'], 2.5 ); near ( $stats ['variance'], 5 / 3 ); near ( $stats ['q1'], 1.75 );
  check ( $stats ['count'] === 4 && $stats ['missing'] === 1 );
  $offset = padStats ( 'describe', [ 1e12 + 1, 1e12 + 2, 1e12 + 3 ] ); near ( $offset ['variance'], 1 );
  $wide = padStats ( 'describe', [ -1e150, 1e150 ] ); near ( $wide ['variance'], 2e300 );
  check ( padStats ( 'describe', [] ) ['mean'] === NULL ); check ( padStats ( 'describe', [ 1 ] ) ['variance'] === NULL );
  $quantiles = padStats ( 'quantiles', [ 3, 1, 4, 2 ], [ 'probabilities' => [ 0, .25, .5, .75, 1 ] ] );
  check ( array_column ( $quantiles ['quantiles'], 'value' ) === [ 1.0, 1.75, 2.5, 3.25, 4.0 ] );
  $histogram = padStats ( 'histogram', [ -1, 0, 1, 2, 3, 4, 5 ], [ 'edges' => [ 0, 2, 4 ] ] );
  check ( array_column ( $histogram ['bins'], 'count' ) === [ 2, 3 ] && $histogram ['underflow'] === 1 && $histogram ['overflow'] === 1 );
  check ( padStats ( 'histogram', [ 5, 5, 5 ] ) ['bins'] [0] ['count'] === 3 );
  $regression = padStats ( 'regression', [ [ 'x' => 1e12 + 1, 'y' => 3 ], [ 'x' => 1e12 + 2, 'y' => 5 ], [ 'x' => 1e12 + 3, 'y' => 7 ], [ 'x' => NULL, 'y' => 8 ] ] );
  near ( $regression ['slope'], 2 ); near ( $regression ['intercept'], 1 - 2e12 ); near ( $regression ['r'], 1 ); check ( $regression ['missing'] === 1 );
  check ( padStats ( 'regression', [ [ 'x' => 1, 'y' => 1 ], [ 'x' => 1, 'y' => 2 ] ] ) ['status'] === 'degenerate' );
  check ( padStats ( 'outliers', [ 1, 2, 2, 3, NULL, 100 ] ) ['outliers'] === [ [ 'index' => 5, 'value' => 100.0 ] ] );
  foreach ( [ [ 'describe', [ INF ] ], [ 'describe', [ '3' ] ], [ 'describe', [ NULL ], [ 'missing' => 'error' ] ], [ 'histogram', [], [ 'edges' => [ 1, 1 ] ] ], [ 'quantiles', [], [ 'probabilities' => [ 2 ] ] ], [ 'outliers', [], [ 'factor' => -1 ] ] ] as $args ) rejects ( fn () => padStats ( ...$args ) );

  // All 64 directed graphs on three nodes, independently checked by transitive
  // closure and exhaustive simple paths, including cycles and unreachable nodes.
  $nodes = [ 'a', 'b', 'c' ]; $possible = [];
  for ( $i = 0; $i < 3; $i++ ) for ( $j = 0; $j < 3; $j++ ) if ( $i !== $j ) $possible [] = [ $i, $j ];
  for ( $mask = 0; $mask < 64; $mask++ ) {
    $edges = []; $weighted = []; $reach = array_fill ( 0, 3, array_fill ( 0, 3, FALSE ) ); $distance = array_fill ( 0, 3, array_fill ( 0, 3, INF ) );
    for ( $i = 0; $i < 3; $i++ ) $distance [$i] [$i] = 0;
    foreach ( $possible as $bit => [ $a, $b ] ) if ( $mask & ( 1 << $bit ) ) { $edges [] = [ $nodes [$a], $nodes [$b] ]; $weight = $bit + 1; $weighted [] = [ $nodes [$a], $nodes [$b], $weight ]; $reach [$a] [$b] = TRUE; $distance [$a] [$b] = $weight; }
    for ( $k = 0; $k < 3; $k++ ) for ( $i = 0; $i < 3; $i++ ) for ( $j = 0; $j < 3; $j++ ) { $reach [$i] [$j] = $reach [$i] [$j] || ( $reach [$i] [$k] && $reach [$k] [$j] ); $distance [$i] [$j] = min ( $distance [$i] [$j], $distance [$i] [$k] + $distance [$k] [$j] ); }
    $cycle = $reach [0] [0] || $reach [1] [1] || $reach [2] [2]; $order = padGraph ( 'toposort', $nodes, $edges );
    check ( ( $order ['status'] === 'cycle' ) === $cycle );
    if ( ! $cycle ) { foreach ( $edges as [ $a, $b ] ) check ( array_search ( $a, $order ['order'] ) < array_search ( $b, $order ['order'] ) ); }
    else { check ( $order ['cycle'] [0] === end ( $order ['cycle'] ) ); for ( $i = 1; $i < count ( $order ['cycle'] ); $i++ ) check ( in_array ( [ $order ['cycle'] [$i - 1], $order ['cycle'] [$i] ], $edges, TRUE ) ); }
    for ( $source = 0; $source < 3; $source++ ) {
      $shortest = padGraph ( 'shortestpath', $nodes, $weighted, [ 'source' => $nodes [$source] ] );
      foreach ( $shortest ['distances'] as $target => $row ) { if ( is_finite ( $distance [$source] [$target] ) ) near ( $row ['distance'], $distance [$source] [$target] ); else check ( $row ['distance'] === NULL ); }
    }
    $weak = array_fill ( 0, 3, array_fill ( 0, 3, FALSE ) ); foreach ( $possible as $bit => [ $a, $b ] ) if ( $mask & ( 1 << $bit ) ) $weak [$a] [$b] = $weak [$b] [$a] = TRUE;
    for ( $i = 0; $i < 3; $i++ ) $weak [$i] [$i] = TRUE;
    for ( $k = 0; $k < 3; $k++ ) for ( $i = 0; $i < 3; $i++ ) for ( $j = 0; $j < 3; $j++ ) $weak [$i] [$j] = $weak [$i] [$j] || ( $weak [$i] [$k] && $weak [$k] [$j] );
    $components = padGraph ( 'components', $nodes, $edges ) ['components'];
    for ( $i = 0; $i < 3; $i++ ) for ( $j = 0; $j < 3; $j++ ) { $together = FALSE; foreach ( $components as $component ) if ( in_array ( $nodes [$i], $component ) && in_array ( $nodes [$j], $component ) ) $together = TRUE; check ( $together === $weak [$i] [$j] ); }
    $tasks = array_map ( fn ( $id, $i ) => [ 'id' => $id, 'duration' => $i + 1 ], $nodes, [ 0, 1, 2 ] ); $critical = padGraph ( 'criticalpath', $tasks, $edges );
    check ( ( $critical ['status'] === 'cycle' ) === $cycle );
    if ( ! $cycle ) {
      $longest = function ( $node ) use ( &$longest, $edges, $nodes ) { $best = 0; foreach ( $edges as [ $a, $b ] ) if ( $a === $nodes [$node] ) $best = max ( $best, $longest ( array_search ( $b, $nodes ) ) ); return $node + 1 + $best; };
      near ( $critical ['duration'], max ( $longest ( 0 ), $longest ( 1 ), $longest ( 2 ) ) );
    }
  }
  rejects ( fn () => padGraph ( 'toposort', [ 'a', 'a' ], [] ) ); rejects ( fn () => padGraph ( 'shortestpath', [ 'a', 'b' ], [ [ 'a', 'b', -1 ] ], [ 'source' => 'a' ] ) );
  check ( padGraph ( 'shortestpath', [ 'a', 'b' ], [], [ 'source' => 'a', 'target' => 'b' ] ) ['status'] === 'unreachable' );

  $schema = [ 'type' => 'object', 'required' => [ 'name' ], 'properties' => [ 'name' => [ 'type' => 'string', 'minLength' => 1 ], 'score' => [ 'type' => 'integer', 'minimum' => 0 ] ], 'additionalProperties' => FALSE ];
  check ( padSchema ( 'jsonschema', (object) [ 'name' => 'Ada', 'score' => 2 ], $schema ) ['valid'] );
  check ( padSchema ( 'record', [ 'name' => 'Ada', 'score' => 2 ], $schema ) ['valid'] );
  check ( padSchema ( 'rowset', [ [ 'name' => 'Ada' ], [ 'name' => 'Bo', 'score' => -1 ] ], $schema ) ['issues'] [0] ['path'] === '$[1].score' );
  check ( padSchema ( 'record', [ 'name' => NULL ], $schema ) ['issues'] [0] ['code'] === 'type' );
  check ( padSchema ( 'record', [], $schema ) ['issues'] [0] ['code'] === 'required' );
  check ( ! padSchema ( 'jsonschema', [], [ 'type' => 'object' ] ) ['valid'] );
  check ( ! padSchema ( 'jsonschema', 1, [ 'enum' => [ 1.0 ] ] ) ['valid'] );
  check ( ! padSchema ( 'jsonschema', 1, [ '$ref' => 'https://invalid.example/' ] ) ['valid'] );
  check ( ! padSchema ( 'jsonschema', INF, [] ) ['valid'] );
  check ( ! padSchema ( 'jsonschema', "\xff", [] ) ['valid'] );
  $deep = NULL; for ( $i = 0; $i < 66; $i++ ) $deep = [ $deep ]; check ( padSchema ( 'jsonschema', $deep, [] ) ['issues'] [0] ['code'] === 'depth' );
  require PAD . 'subs/ai/_lib/json.php';
  check ( padAiJsonSchemaError ( [ 'type' => NULL ] ) === 'schema.type must be a type name' );
  check ( padAiJsonValueError ( (object) [ 'name' => '' ], $schema ) === '$.name is shorter than minLength' );
  check ( padAiJsonValueError ( (object) [], $schema ) === '$.name is required' );

  $text = padTextAnalysis ( 'counts', "Cafe\u{0301} café\n\n👨‍👩‍👧‍👦" );
  check ( $text ['tokens'] === 2 && $text ['uniqueTokens'] === 1 && $text ['paragraphs'] === 2 );
  check ( padTextAnalysis ( 'counts', '👨‍👩‍👧‍👦' ) ['graphemes'] === 1 );
  check ( padTextAnalysis ( 'counts', '' ) ['paragraphs'] === 0 );
  $grams = padTextAnalysis ( 'ngrams', 'A b a b a', [ 'n' => 2 ] ); check ( $grams ['ngrams'] === [ [ 'ngram' => 'a b', 'count' => 2 ], [ 'ngram' => 'b a', 'count' => 2 ] ] );
  near ( padTextAnalysis ( 'similarity', [ 'a' => 'one two', 'b' => 'two three' ] ) ['pairs'] [0] ['score'], 1 / 3 );
  near ( padTextAnalysis ( 'similarity', [ 'one one two', 'two three' ], [ 'metric' => 'cosine' ] ) ['pairs'] [0] ['score'], 1 / sqrt ( 10 ) );
  near ( padTextAnalysis ( 'similarity', [ '', '' ] ) ['pairs'] [0] ['score'], 1 );
  near ( padTextAnalysis ( 'similarity', [ '', 'hello' ] ) ['pairs'] [0] ['score'], 0 );
  rejects ( fn () => padTextAnalysis ( 'counts', "\xff" ) ); rejects ( fn () => padTextAnalysis ( 'ngrams', '', [ 'n' => 0 ] ) ); rejects ( fn () => padTextAnalysis ( 'similarity', array_fill ( 0, 201, '' ) ) );

  // Every family of nonempty subsets of three columns: brute-force row selections
  // prove feasibility independently from Algorithm X's column-driven search.
  for ( $mask = 0; $mask < 128; $mask++ ) {
    $coverRows = []; $sets = [];
    for ( $set = 1; $set < 8; $set++ ) if ( $mask & ( 1 << ( $set - 1 ) ) ) { $sets [] = $set; $coverRows ['r' . $set] = array_values ( array_filter ( [ 'a', 'b', 'c' ], fn ( $id ) => $set & ( 1 << array_search ( $id, [ 'a', 'b', 'c' ] ) ) ) ); }
    $exists = FALSE;
    for ( $selection = 0; $selection < ( 1 << count ( $sets ) ); $selection++ ) { $covered = 0; $disjoint = TRUE; foreach ( $sets as $i => $set ) if ( $selection & ( 1 << $i ) ) { if ( $covered & $set ) $disjoint = FALSE; $covered |= $set; } if ( $disjoint && $covered === 7 ) $exists = TRUE; }
    $cover = padSolve ( 'exactcover', [ 'columns' => [ 'a', 'b', 'c' ], 'rows' => $coverRows ] ); check ( ( $cover ['status'] === 'solved' ) === $exists );
    if ( $exists ) { $covered = []; foreach ( $cover ['solution'] as $id ) $covered = array_merge ( $covered, $coverRows [$id] ); sort ( $covered ); check ( $covered === [ 'a', 'b', 'c' ] ); }
  }
  // Every 3x3 bipartite graph, brute-force partial injection oracle.
  $permutations = [ [ 0, 1, 2 ], [ 0, 2, 1 ], [ 1, 0, 2 ], [ 1, 2, 0 ], [ 2, 0, 1 ], [ 2, 1, 0 ] ];
  for ( $mask = 0; $mask < 512; $mask++ ) {
    $edges = []; $costs = []; for ( $i = 0; $i < 3; $i++ ) { $costs [$i] = []; for ( $j = 0; $j < 3; $j++ ) { $allowed = (bool) ( $mask & ( 1 << ( 3 * $i + $j ) ) ); if ( $allowed ) $edges [] = [ $nodes [$i], $nodes [$j] ]; $costs [$i] [$j] = $allowed ? ( ( $i * 7 + $j * 5 + $mask ) % 11 - 5 ) : NULL; } }
    $maximum = 0; $minimumCost = INF;
    foreach ( $permutations as $permutation ) { $matched = 0; $total = 0; foreach ( $permutation as $i => $j ) if ( $costs [$i] [$j] !== NULL ) { $matched++; $total += $costs [$i] [$j]; } $maximum = max ( $maximum, $matched ); if ( $matched === 3 ) $minimumCost = min ( $minimumCost, $total ); }
    $matching = padSolve ( 'matching', [ 'left' => $nodes, 'right' => $nodes, 'edges' => $edges ] ); check ( $matching ['cardinality'] === $maximum );
    $assignment = padSolve ( 'assignment', [ 'rows' => $nodes, 'columns' => $nodes, 'costs' => $costs ] ); check ( ( $assignment ['status'] === 'solved' ) === is_finite ( $minimumCost ) );
    if ( is_finite ( $minimumCost ) ) near ( $assignment ['cost'], $minimumCost );
  }
  check ( padSolve ( 'assignment', [ 'rows' => [ 'x' ], 'columns' => [ 'a', 'b' ], 'costs' => [ [ 2, -3 ] ] ] ) ['cost'] === -3 );
  $problem = [ 'columns' => [ 'a' ], 'rows' => [ 'r' => [ 'a' ] ] ];
  check ( padSolve ( 'exactcover', $problem, [ 'maxSteps' => 1 ] ) ['status'] === 'limit' );
  check ( padSolve ( 'exactcover', $problem, [ 'cancel' => fn () => TRUE ] ) ['status'] === 'cancelled' );
  rejects ( fn () => padSolve ( 'assignment', [ 'rows' => [ 'x' ], 'columns' => [ 'y' ], 'costs' => [ [ INF ] ] ] ) );

  foreach ( [ [ 1, 'h', 's', 3600 ], [ 36, 'km/h', 'm/s', 10 ], [ 1, 'atm', 'Pa', 101325 ], [ 1, 'kWh', 'J', 3600000 ], [ 1, 'kW', 'W', 1000 ], [ 180, 'deg', 'rad', M_PI ] ] as [ $value, $from, $to, $expected ] ) near ( padConvert ( $value, $from, $to ), $expected );
  $symbols = [];
  foreach ( padUnits () as $family => $definition ) foreach ( $definition ['units'] as $symbol => $unit ) foreach ( array_merge ( [ $symbol ], $unit ['aliases'] ) as $name ) { check ( ! isset ( $symbols [$name] ), 'ambiguous unit ' . $name ); $symbols [$name] = TRUE; }
  $destination = padGeo ( 'destination', [ 179, 0 ], M_PI * 6371008.8 / 90, [ 'bearing' => 90 ] ); near ( $destination [0], -179 ); near ( $destination [1], 0 );
  near ( padGeo ( 'route-length', [ [ 0, 0 ], [ 45, 0 ], [ 90, 0 ] ] ), M_PI * 6371008.8 / 2 );
  near ( padGeo ( 'route-length', [] ), 0 );
  check ( padGeo ( 'nearest', [ 0, 0 ], [ 'first' => [ 1, 0 ], 'second' => [ -1, 0 ] ] ) ['id'] === 'first' );
  check ( padGeo ( 'nearest', [ 0, 0 ], [] ) === NULL );
  rejects ( fn () => padGeo ( 'destination', [ 0, 90 ], 100, [ 'bearing' => 90 ] ) );
  rejects ( fn () => padGeo ( 'route-length', [], NULL, [ 'unit' => 'invalid' ] ) );
  foreach ( [ [ 'triangular', [ 'min' => 0, 'mode' => .25, 'max' => 1 ], 1.25 / 3, ( 1 + .0625 - .25 ) / 18 ], [ 'lognormal', [ 'mu' => 0, 'sigma' => .5 ], exp ( .125 ), ( exp ( .25 ) - 1 ) * exp ( .25 ) ], [ 'beta', [ 'alpha' => .5, 'beta' => .5 ], .5, .125 ], [ 'beta', [ 'alpha' => 2, 'beta' => 5 ], 2 / 7, 10 / 392 ] ] as [ $kind, $options, $mean, $variance ] ) {
    $samples = padSample ( $kind, 10000, $options + [ 'seed' => 71 ] ); $moments = padStats ( 'describe', $samples );
    near ( $moments ['mean'], $mean, .025 ); near ( $moments ['variance'], $variance, .025 );
    check ( array_slice ( $samples, 0, 10 ) === padSample ( $kind, 10, $options + [ 'seed' => 71 ] ) );
    if ( $kind !== 'lognormal' ) check ( min ( $samples ) >= 0 && max ( $samples ) <= 1 ); else check ( min ( $samples ) > 0 );
  }
  padFakeSeed ( 42 ); $state = padFakeState (); padSample ( 'beta', 10, [ 'seed' => 12 ] ); check ( padFakeState () === $state );
  rejects ( fn () => padSample ( 'beta', 1, [ 'random' => fn () => 0 ] ) );
  rejects ( fn () => padSample ( 'beta', 20000 ) );
  rejects ( fn () => padSample ( 'triangular', 0, [ 'mode' => 2 ] ) );
  rejects ( fn () => padSample ( 'lognormal', 1, [ 'mu' => 700, 'sigma' => 100, 'random' => fn () => .99 ] ) );
  echo "analysis algorithms: $checks assertions; exhaustive tiny graphs, exact covers, matchings and assignments; moments, Unicode, schemas, units and geography passed\n";
?>
