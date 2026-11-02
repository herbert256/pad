<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $path ) { return scandir ( $path ); }
  function padError ( $message ) { throw new InvalidArgumentException ( $message ); }
  require PAD . 'lib/subsystem.php';
  require PAD . 'lib/constraints.php';
  $checks = $cases = 0;
  function check ( $condition, $message ) { global $checks; $checks++; if ( ! $condition ) throw new RuntimeException ( $message ); }
  function rejects ( $call ) { try { $call (); } catch ( InvalidArgumentException $e ) { check ( TRUE, 'precision rejection' ); return; } throw new RuntimeException ( 'unsupported precision accepted' ); }
  function solveCosts ( $costs, $columns = NULL, $options = [] ) {
    $n = count ( $costs ); $m = $columns ?? ( $n ? count ( $costs [0] ) : 0 );
    return padSolve ( 'assignment', [ 'rows' => array_map ( fn ( $i ) => 'r' . $i, $n ? range ( 0, $n - 1 ) : [] ),
      'columns' => array_map ( fn ( $i ) => 'c' . $i, $m ? range ( 0, $m - 1 ) : [] ), 'costs' => $costs ], $options );
  }
  // Enumerate injections directly, using integer input units for an independent
  // objective. No potentials, reduced costs or optimizer helpers are reused.
  function oracle ( $costs ) {
    $best = NULL;
    $visit = function ( $row, $used, $total ) use ( &$visit, &$best, $costs ) {
      if ( $row === count ( $costs ) ) { if ( $best === NULL || $total < $best ) $best = $total; return; }
      foreach ( $costs [$row] as $column => $cost ) if ( $cost !== NULL && ! isset ( $used [$column] ) ) $visit ( $row + 1, $used + [ $column => TRUE ], $total + $cost );
    };
    $visit ( 0, [], 0 ); return $best;
  }
  function compare ( $integerCosts, $unit = 1 ) {
    global $cases; $cases++;
    $costs = $unit === 1 ? $integerCosts : array_map ( fn ( $row ) => array_map ( fn ( $v ) => $v === NULL ? NULL : (float) ( $v / $unit ), $row ), $integerCosts );
    $expected = oracle ( $integerCosts ); $result = solveCosts ( $costs );
    check ( ( $result ['status'] === 'solved' ) === ( $expected !== NULL ), 'independent injection feasibility' );
    if ( $expected === NULL ) return;
    $actual = 0; $selected = [];
    foreach ( $result ['solution'] as $i => $edge ) { $column = (int) substr ( $edge ['column'], 1 ); check ( ! isset ( $selected [$column] ) && $integerCosts [$i] [$column] !== NULL, 'injective allowed edges' ); $selected [$column] = TRUE; $actual += $integerCosts [$i] [$column]; }
    check ( count ( $result ['solution'] ) === count ( $integerCosts ) && $actual === $expected, 'exact minimum matches independent oracle' );
    check ( is_int ( $result ['scaledCost'] ) && is_int ( $result ['costScale'] ), 'exact objective metadata uses integers' );
    check ( $result ['scaledCost'] * $unit === $expected * $result ['costScale'], 'scaled objective matches exact integer units' );
    check ( $result ['costScale'] === 1 ? is_int ( $result ['cost'] ) : is_float ( $result ['cost'] ), 'public cost representation follows scale' );
    check ( json_encode ( $result ) !== FALSE, 'finite serializable result' );
  }

  $cancellation = [ [ 10000000000000000, 10000000000000000, 1, 0 ], [ 1, -10, 10000000000000000, -10000000000000000 ], [ 10000000000000000, NULL, 10, 10000000000000000 ] ];
  compare ( $cancellation );
  $exact = solveCosts ( $cancellation );
  check ( array_column ( $exact ['solution'], 'column' ) === [ 'c3', 'c1', 'c2' ] && $exact ['cost'] === 0 && $exact ['scaledCost'] === 0 && $exact ['costScale'] === 1, 'large cancellation regression chooses true minimum' );
  $decimal = solveCosts ( [ [ .1, .25 ], [ .3, .5 ] ] );
  check ( $decimal ['costScale'] === 100 && $decimal ['scaledCost'] === 55 && $decimal ['cost'] === .55, 'fractional public result shape' );
  check ( array_column ( $decimal ['solution'], 'cost' ) === [ .25, .3 ], 'original per-edge fractional costs retained' );
  $tiny = solveCosts ( [ [ 1e-9 ] ] ); check ( $tiny ['scaledCost'] === 1 && $tiny ['costScale'] === 1000000000, 'nine decimal places supported' );
  check ( solveCosts ( [ [ 1.0 ] ] ) ['cost'] === 1, 'integral float normalizes exactly' );
  foreach ( [ 1 / 3, .1 + .2, 1e-10, 1e100, PHP_INT_MAX, PHP_INT_MIN, INF, NAN ] as $value ) rejects ( fn () => solveCosts ( [ [ $value ] ] ) );
  rejects ( fn () => solveCosts ( [ [ 10000000000000000, .000000001 ] ] ) );
  $bound = intdiv ( PHP_INT_MAX, 8 * 3 );
  check ( solveCosts ( [ [ $bound ] ] ) ['cost'] === $bound && solveCosts ( [ [ -$bound ] ] ) ['cost'] === -$bound, 'inclusive exact magnitude bound' );
  rejects ( fn () => solveCosts ( [ [ $bound + 1 ] ] ) );
  check ( solveCosts ( [] ) ['cost'] === 0 && solveCosts ( [] ) ['costScale'] === 1, 'empty assignment has exact zero objective' );
  check ( solveCosts ( [ [] ] ) ['status'] === 'infeasible', 'more rows than columns' );
  check ( solveCosts ( [ [ 1, 2 ], [ 3, 4 ] ], NULL, [ 'maxSteps' => 1 ] ) ['status'] === 'limit', 'work limit remains explicit' );
  check ( solveCosts ( [ [ 1 ] ], NULL, [ 'cancel' => fn () => TRUE ] ) ['status'] === 'cancelled', 'cancellation remains explicit' );

  $alphabet = [ NULL, -2, 0, 3 ];
  for ( $encoded = 0; $encoded < 4096; $encoded++ ) {
    $remaining = $encoded; $costs = [];
    for ( $i = 0; $i < 2; $i++ ) for ( $j = 0; $j < 3; $j++ ) { $costs [$i] [$j] = $alphabet [$remaining % 4]; $remaining = intdiv ( $remaining, 4 ); }
    compare ( $costs );
  }
  mt_srand ( 27182 ); $alphabet = [ -10000000000000000, -10, -1, 0, 1, 10, 10000000000000000, NULL ];
  for ( $case = 0; $case < 5000; $case++ ) {
    $costs = []; for ( $i = 0; $i < 3; $i++ ) for ( $j = 0; $j < 4; $j++ ) $costs [$i] [$j] = $alphabet [mt_rand ( 0, count ( $alphabet ) - 1 )]; compare ( $costs );
  }
  for ( $case = 0; $case < 500; $case++ ) {
    $costs = []; for ( $i = 0; $i < 3; $i++ ) for ( $j = 0; $j < 5; $j++ ) $costs [$i] [$j] = mt_rand ( 0, 4 ) ? mt_rand ( -25, 25 ) : NULL; compare ( $costs, 100 );
  }
  echo "assignment precision: $checks assertions, $cases independent rectangular oracle cases passed\n";
?>
