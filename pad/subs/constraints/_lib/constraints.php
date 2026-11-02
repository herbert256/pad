<?php
  class PadSolveStop extends RuntimeException {}

  function padSolveTick ( &$state, $amount = 1 ) {
    if ( $state ['cancel'] !== NULL && ( $state ['cancel'] ) () ) throw new PadSolveStop ( 'cancelled' );
    if ( $state ['steps'] + $amount > $state ['max'] ) throw new PadSolveStop ( 'limit' );
    $state ['steps'] += $amount;
  }

  function padSolveIds ( $ids, $limit, $name ) {
    if ( ! is_array ( $ids ) || ! array_is_list ( $ids ) || count ( $ids ) > $limit ) throw new InvalidArgumentException ( "$name must be a list of at most $limit IDs" );
    $map = [];
    foreach ( $ids as $i => $id ) {
      if ( ! is_string ( $id ) || $id === '' || strlen ( $id ) > 256 || isset ( $map ['#' . $id] ) )
        throw new InvalidArgumentException ( "$name IDs must be unique nonempty strings of at most 256 bytes" );
      $map ['#' . $id] = $i;
    }
    return $map;
  }

  function padSolveProblemKeys ( $problem, $keys ) {
    if ( array_diff ( array_keys ( $problem ), $keys ) || array_diff ( $keys, array_keys ( $problem ) ) )
      throw new InvalidArgumentException ( 'problem requires exactly: ' . implode ( ', ', $keys ) );
  }

  function padSolveExactcover ( $problem, $options, &$state ) {
    padSolveProblemKeys ( $problem, [ 'columns', 'rows' ] );
    $columns = padSolveIds ( $problem ['columns'], 256, 'columns' );
    if ( ! is_array ( $problem ['rows'] ) || count ( $problem ['rows'] ) > 4096 ) throw new InvalidArgumentException ( 'rows must contain at most 4096 identity-keyed column lists' );
    $rows = []; $incidences = 0;
    foreach ( $problem ['rows'] as $id => $row ) {
      $map = padSolveIds ( $row, 256, 'row columns' );
      if ( ! $map || array_diff_key ( $map, $columns ) ) throw new InvalidArgumentException ( 'each exact-cover row must contain known columns and cannot be empty' );
      $incidences += count ( $map );
      if ( $incidences > 65536 ) throw new InvalidArgumentException ( 'exact-cover problem exceeds 65536 incidences' );
      $rows [$id] = $map;
    }
    $search = function ( $remaining, $available, $chosen ) use ( &$search, &$state ) {
      padSolveTick ( $state );
      if ( ! $remaining ) return $chosen;
      $byColumn = array_fill_keys ( array_keys ( $remaining ), [] ); $valid = [];
      foreach ( $available as $id => $row ) {
        padSolveTick ( $state, count ( $row ) + 1 );
        if ( array_diff_key ( $row, $remaining ) ) continue;
        $valid [$id] = $row;
        foreach ( $row as $column => $unused ) $byColumn [$column] [] = $id;
      }
      $candidates = NULL;
      foreach ( $byColumn as $ids ) if ( $candidates === NULL || count ( $ids ) < count ( $candidates ) ) $candidates = $ids;
      foreach ( $candidates as $id ) {
        $result = $search ( array_diff_key ( $remaining, $valid [$id] ), $valid, array_merge ( $chosen, [ $id ] ) );
        if ( $result !== NULL ) return $result;
      }
      return NULL;
    };
    $solution = $search ( $columns, $rows, [] );
    return [ 'status' => $solution === NULL ? 'infeasible' : 'solved', 'solution' => $solution, 'method' => 'Algorithm X; fewest candidate rows first' ];
  }

  function padSolveMatching ( $problem, $options, &$state ) {
    padSolveProblemKeys ( $problem, [ 'left', 'right', 'edges' ] );
    $left = padSolveIds ( $problem ['left'], 500, 'left' ); $right = padSolveIds ( $problem ['right'], 500, 'right' );
    $perfect = $options ['requirePerfect'] ?? FALSE;
    if ( ! is_bool ( $perfect ) ) throw new InvalidArgumentException ( 'requirePerfect must be boolean' );
    if ( ! is_array ( $problem ['edges'] ) || ! array_is_list ( $problem ['edges'] ) || count ( $problem ['edges'] ) > 20000 )
      throw new InvalidArgumentException ( 'edges must be a list of at most 20000 left/right pairs' );
    $adj = array_fill ( 0, count ( $left ), [] ); $seenEdges = [];
    foreach ( $problem ['edges'] as $edge ) {
      if ( ! is_array ( $edge ) || ! array_is_list ( $edge ) || count ( $edge ) !== 2 || ! is_string ( $edge [0] ) || ! is_string ( $edge [1] ) || ! isset ( $left ['#' . $edge [0]], $right ['#' . $edge [1]] ) )
        throw new InvalidArgumentException ( 'matching edges must reference known left/right IDs' );
      $a = $left ['#' . $edge [0]]; $b = $right ['#' . $edge [1]]; $key = "$a:$b";
      if ( isset ( $seenEdges [$key] ) ) throw new InvalidArgumentException ( 'duplicate matching edge' );
      $seenEdges [$key] = TRUE; $adj [$a] [] = $b;
    }
    foreach ( $adj as &$neighbors ) sort ( $neighbors, SORT_NUMERIC ); unset ( $neighbors );
    $matched = array_fill ( 0, count ( $right ), NULL );
    $augment = function ( $a, &$visited ) use ( &$augment, &$matched, &$state, $adj ) {
      padSolveTick ( $state );
      foreach ( $adj [$a] as $b ) {
        padSolveTick ( $state );
        if ( isset ( $visited [$b] ) ) continue; $visited [$b] = TRUE;
        if ( $matched [$b] === NULL || $augment ( $matched [$b], $visited ) ) { $matched [$b] = $a; return TRUE; }
      }
      return FALSE;
    };
    foreach ( $problem ['left'] as $a => $id ) { $visited = []; $augment ( $a, $visited ); }
    $byLeft = []; foreach ( $matched as $b => $a ) if ( $a !== NULL ) $byLeft [$a] = $b;
    $solution = []; $unmatched = [];
    foreach ( $problem ['left'] as $a => $id ) {
      if ( isset ( $byLeft [$a] ) ) $solution [] = [ 'left' => $id, 'right' => $problem ['right'] [$byLeft [$a]] ];
      else $unmatched [] = $id;
    }
    return [ 'status' => $perfect && $unmatched ? 'infeasible' : 'solved', 'solution' => $solution, 'cardinality' => count ( $solution ), 'unmatchedLeft' => $unmatched, 'method' => 'maximum bipartite matching; augmenting paths' ];
  }

  // Preserve an exact objective. Floats must round-trip through one bounded
  // decimal scale; silently rounding arbitrary floating costs changes optima.
  function padSolveAssignmentCosts ( $costs, $n, $m ) {
    $bound = intdiv ( PHP_INT_MAX, 8 * ( $n + $m + 1 ) );
    for ( $places = 0; $places <= 9; $places++ ) {
      $scale = 10 ** $places; $scaled = []; $valid = TRUE;
      foreach ( $costs as $i => $row ) {
        $scaled [$i] = [];
        foreach ( $row as $cost ) {
          if ( $cost === NULL ) { $scaled [$i] [] = NULL; continue; }
          if ( is_int ( $cost ) ) {
            $limit = intdiv ( $bound, $scale );
            if ( $cost < -$limit || $cost > $limit ) { $valid = FALSE; break 2; }
            $scaled [$i] [] = $cost * $scale;
          } else {
            $rounded = round ( $cost * $scale );
            if ( ! is_finite ( $rounded ) || $rounded < -$bound || $rounded > $bound ) { $valid = FALSE; break 2; }
            $integer = (int) $rounded;
            if ( $integer < -$bound || $integer > $bound || (float) ( $integer / $scale ) !== $cost ) { $valid = FALSE; break 2; }
            $scaled [$i] [] = $integer;
          }
        }
      }
      if ( $valid ) return [ $scaled, $scale ];
    }
    throw new InvalidArgumentException ( "assignment costs require an exact common decimal scale of at most 9 places and scaled magnitude at most $bound" );
  }

  function padSolveIntegerAdd ( $a, $b ) {
    if ( ( $b > 0 && $a > PHP_INT_MAX - $b ) || ( $b < 0 && $a < PHP_INT_MIN - $b ) )
      throw new InvalidArgumentException ( 'assignment arithmetic exceeds the exact integer range' );
    return $a + $b;
  }

  function padSolveIntegerSubtract ( $a, $b ) {
    if ( ( $b > 0 && $a < PHP_INT_MIN + $b ) || ( $b < 0 && $a > PHP_INT_MAX + $b ) )
      throw new InvalidArgumentException ( 'assignment arithmetic exceeds the exact integer range' );
    return $a - $b;
  }

  function padSolveAssignment ( $problem, $options, &$state ) {
    padSolveProblemKeys ( $problem, [ 'rows', 'columns', 'costs' ] );
    $rows = padSolveIds ( $problem ['rows'], 128, 'rows' ); $columns = padSolveIds ( $problem ['columns'], 128, 'columns' );
    $n = count ( $rows ); $m = count ( $columns ); $costs = $problem ['costs'];
    if ( ! is_array ( $costs ) || ! array_is_list ( $costs ) || count ( $costs ) !== $n ) throw new InvalidArgumentException ( 'cost matrix must have one list per row' );
    foreach ( $costs as $row ) {
      if ( ! is_array ( $row ) || ! array_is_list ( $row ) || count ( $row ) !== $m ) throw new InvalidArgumentException ( 'cost matrix must have one value per column' );
      foreach ( $row as $cost ) if ( $cost !== NULL && ( ( ! is_int ( $cost ) && ! is_float ( $cost ) ) || ! is_finite ( (float) $cost ) ) )
        throw new InvalidArgumentException ( 'costs must be native finite numbers, or null for forbidden assignments' );
    }
    [ $exactCosts, $scale ] = padSolveAssignmentCosts ( $costs, $n, $m );
    padSolveTick ( $state );
    if ( $n > $m ) return [ 'status' => 'infeasible', 'solution' => NULL, 'cost' => NULL ];
    // Rectangular Hungarian algorithm. Potentials use the extra zero column as an
    // augmenting-path sentinel; null edges never enter the finite candidate set.
    $u = array_fill ( 0, $n + 1, 0 ); $v = array_fill ( 0, $m + 1, 0 ); $p = $way = array_fill ( 0, $m + 1, 0 );
    for ( $i = 1; $i <= $n; $i++ ) {
      $p [0] = $i; $j0 = 0; $minv = array_fill ( 0, $m + 1, NULL ); $used = array_fill ( 0, $m + 1, FALSE );
      do {
        $used [$j0] = TRUE; $i0 = $p [$j0]; $delta = NULL; $j1 = 0;
        for ( $j = 1; $j <= $m; $j++ ) if ( ! $used [$j] ) {
          padSolveTick ( $state ); $cost = $exactCosts [$i0 - 1] [$j - 1];
          if ( $cost !== NULL ) { $current = padSolveIntegerSubtract ( padSolveIntegerSubtract ( $cost, $u [$i0] ), $v [$j] ); if ( $minv [$j] === NULL || $current < $minv [$j] ) { $minv [$j] = $current; $way [$j] = $j0; } }
          if ( $minv [$j] !== NULL && ( $delta === NULL || $minv [$j] < $delta ) ) { $delta = $minv [$j]; $j1 = $j; }
        }
        if ( $delta === NULL ) return [ 'status' => 'infeasible', 'solution' => NULL, 'cost' => NULL ];
        for ( $j = 0; $j <= $m; $j++ ) {
          if ( $used [$j] ) { $u [$p [$j]] = padSolveIntegerAdd ( $u [$p [$j]], $delta ); $v [$j] = padSolveIntegerSubtract ( $v [$j], $delta ); }
          elseif ( $minv [$j] !== NULL ) $minv [$j] = padSolveIntegerSubtract ( $minv [$j], $delta );
        }
        $j0 = $j1;
      } while ( $p [$j0] !== 0 );
      do { $j1 = $way [$j0]; $p [$j0] = $p [$j1]; $j0 = $j1; } while ( $j0 !== 0 );
    }
    $byRow = []; for ( $j = 1; $j <= $m; $j++ ) if ( $p [$j] ) $byRow [$p [$j] - 1] = $j - 1;
    ksort ( $byRow ); $solution = []; $total = 0;
    foreach ( $byRow as $i => $j ) { $cost = $costs [$i] [$j]; $total = padSolveIntegerAdd ( $total, $exactCosts [$i] [$j] ); $solution [] = [ 'row' => $problem ['rows'] [$i], 'column' => $problem ['columns'] [$j], 'cost' => $cost ]; }
    return [ 'status' => 'solved', 'solution' => $solution, 'cost' => $scale === 1 ? $total : (float) ( $total / $scale ),
      'scaledCost' => $total, 'costScale' => $scale, 'method' => 'minimum-cost injective assignment; exact scaled-integer Hungarian' ];
  }
?>
