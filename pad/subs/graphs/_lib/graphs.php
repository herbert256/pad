<?php
  function padGraphNormalize ( $nodes, $edges, $options, $kind ) {
    if ( ! is_array ( $nodes ) || ! array_is_list ( $nodes ) || count ( $nodes ) > 1000 || ! is_array ( $edges ) || ! array_is_list ( $edges ) || count ( $edges ) > 10000 )
      throw new InvalidArgumentException ( 'expected lists of at most 1000 nodes and 10000 edges' );
    $directed = $options ['directed'] ?? TRUE;
    if ( ! is_bool ( $directed ) || ( ! $directed && in_array ( $kind, [ 'toposort', 'criticalpath' ], TRUE ) ) )
      throw new InvalidArgumentException ( 'directed must be boolean; DAG operations require directed=true' );
    $ids = []; $index = []; $durations = [];
    foreach ( $nodes as $i => $node ) {
      $id = is_array ( $node ) ? ( $node ['id'] ?? NULL ) : $node;
      if ( ! is_string ( $id ) || $id === '' || strlen ( $id ) > 256 || isset ( $index ['#' . $id] ) )
        throw new InvalidArgumentException ( 'node IDs must be unique nonempty strings of at most 256 bytes' );
      if ( is_array ( $node ) && array_diff ( array_keys ( $node ), [ 'id', 'duration' ] ) ) throw new InvalidArgumentException ( 'unknown node field' );
      $duration = is_array ( $node ) ? ( $node ['duration'] ?? 0 ) : 0;
      if ( ( ! is_int ( $duration ) && ! is_float ( $duration ) ) || ! is_finite ( (float) $duration ) || $duration < 0 )
        throw new InvalidArgumentException ( 'node duration must be a finite nonnegative native number' );
      $ids [] = $id; $index ['#' . $id] = $i; $durations [] = (float) $duration;
    }
    $adj = $reverse = array_fill ( 0, count ( $ids ), [] ); $normalized = []; $seen = [];
    foreach ( $edges as $edge ) {
      if ( ! is_array ( $edge ) ) throw new InvalidArgumentException ( 'an edge must be [from,to,weight?] or a from/to/weight record' );
      if ( array_is_list ( $edge ) ) {
        if ( count ( $edge ) < 2 || count ( $edge ) > 3 ) throw new InvalidArgumentException ( 'edge tuple needs two or three values' );
        [ $from, $to ] = $edge; $weight = $edge [2] ?? 1;
      } else {
        if ( array_diff ( array_keys ( $edge ), [ 'from', 'to', 'weight' ] ) ) throw new InvalidArgumentException ( 'unknown edge field' );
        $from = $edge ['from'] ?? NULL; $to = $edge ['to'] ?? NULL; $weight = $edge ['weight'] ?? 1;
      }
      if ( ! is_string ( $from ) || ! is_string ( $to ) || ! isset ( $index ['#' . $from], $index ['#' . $to] ) )
        throw new InvalidArgumentException ( 'edge endpoint must reference a known node ID' );
      if ( ( ! is_int ( $weight ) && ! is_float ( $weight ) ) || ! is_finite ( (float) $weight ) || $weight < 0 )
        throw new InvalidArgumentException ( 'edge weights must be finite nonnegative native numbers' );
      if ( $kind !== 'shortestpath' && $weight != 1 ) throw new InvalidArgumentException ( 'weights apply only to shortestpath' );
      $a = $index ['#' . $from]; $b = $index ['#' . $to]; $key = $directed ? "$a:$b" : min ( $a, $b ) . ':' . max ( $a, $b );
      if ( isset ( $seen [$key] ) ) throw new InvalidArgumentException ( 'duplicate edge' );
      $seen [$key] = TRUE; $adj [$a] [] = [ $b, (float) $weight ]; $reverse [$b] [] = [ $a, (float) $weight ]; $normalized [] = [ $a, $b ];
      if ( ! $directed && $a !== $b ) { $adj [$b] [] = [ $a, (float) $weight ]; $reverse [$a] [] = [ $b, (float) $weight ]; }
    }
    foreach ( $adj as &$neighbors ) usort ( $neighbors, fn ( $a, $b ) => $a [0] <=> $b [0] ); unset ( $neighbors );
    return compact ( 'ids', 'index', 'durations', 'adj', 'reverse', 'directed', 'normalized' );
  }

  function padGraphOrder ( $graph ) {
    $n = count ( $graph ['ids'] ); $indegree = array_map ( 'count', $graph ['reverse'] ); $done = array_fill ( 0, $n, FALSE ); $order = [];
    for ( $step = 0; $step < $n; $step++ ) {
      $node = NULL;
      for ( $i = 0; $i < $n; $i++ ) if ( ! $done [$i] && ! $indegree [$i] ) { $node = $i; break; }
      if ( $node === NULL ) break;
      $done [$node] = TRUE; $order [] = $node;
      foreach ( $graph ['adj'] [$node] as [ $next ] ) $indegree [$next]--;
    }
    if ( count ( $order ) === $n ) return [ $order, [] ];
    $state = array_fill ( 0, $n, 0 ); $stack = []; $cycle = [];
    $visit = function ( $node ) use ( &$visit, &$state, &$stack, &$cycle, $graph ) {
      $state [$node] = 1; $stack [] = $node;
      foreach ( $graph ['adj'] [$node] as [ $next ] ) {
        if ( $state [$next] === 1 ) { $cycle = array_slice ( $stack, array_search ( $next, $stack, TRUE ) ); $cycle [] = $next; return TRUE; }
        if ( ! $state [$next] && $visit ( $next ) ) return TRUE;
      }
      array_pop ( $stack ); $state [$node] = 2; return FALSE;
    };
    for ( $i = 0; $i < $n; $i++ ) if ( ! $state [$i] && $visit ( $i ) ) break;
    return [ $order, $cycle ];
  }

  function padGraphToposort ( $graph, $options ) {
    [ $order, $cycle ] = padGraphOrder ( $graph );
    return [ 'status' => $cycle ? 'cycle' : 'ok', 'order' => array_map ( fn ( $i ) => $graph ['ids'] [$i], $order ), 'cycle' => array_map ( fn ( $i ) => $graph ['ids'] [$i], $cycle ) ];
  }

  function padGraphComponents ( $graph, $options ) {
    $seen = []; $components = [];
    foreach ( $graph ['ids'] as $i => $id ) {
      if ( isset ( $seen [$i] ) ) continue;
      $queue = [ $i ]; $seen [$i] = TRUE; $component = [];
      for ( $head = 0; $head < count ( $queue ); $head++ ) {
        $node = $queue [$head]; $component [] = $node;
        foreach ( array_merge ( $graph ['adj'] [$node], $graph ['reverse'] [$node] ) as [ $next ] )
          if ( ! isset ( $seen [$next] ) ) { $seen [$next] = TRUE; $queue [] = $next; }
      }
      sort ( $component, SORT_NUMERIC ); $components [] = array_map ( fn ( $i ) => $graph ['ids'] [$i], $component );
    }
    return [ 'status' => 'ok', 'method' => $graph ['directed'] ? 'weakly connected' : 'connected', 'components' => $components ];
  }

  function padGraphShortestpath ( $graph, $options ) {
    foreach ( [ 'source', 'target' ] as $key )
      if ( ( $key === 'source' || array_key_exists ( $key, $options ) ) && ( ! is_string ( $options [$key] ?? NULL ) || ! isset ( $graph ['index'] ['#' . $options [$key]] ) ) )
        throw new InvalidArgumentException ( "$key must reference a known node ID" );
    $n = count ( $graph ['ids'] ); $source = $graph ['index'] ['#' . $options ['source']];
    $distance = array_fill ( 0, $n, INF ); $parents = array_fill ( 0, $n, NULL ); $done = array_fill ( 0, $n, FALSE ); $distance [$source] = 0.0; $overflow = [];
    for ( $step = 0; $step < $n; $step++ ) {
      $node = NULL; $best = INF;
      for ( $i = 0; $i < $n; $i++ ) if ( ! $done [$i] && $distance [$i] < $best ) { $node = $i; $best = $distance [$i]; }
      if ( $node === NULL ) break;
      $done [$node] = TRUE;
      foreach ( $graph ['adj'] [$node] as [ $next, $weight ] ) {
        if ( $done [$next] ) continue;
        $candidate = $best + $weight;
        // A longer route may overflow even though another route to this node is
        // finite. Defer failure until all representable candidates are settled.
        if ( ! is_finite ( $candidate ) ) { $overflow [$next] = TRUE; continue; }
        if ( $candidate < $distance [$next] ) { $distance [$next] = $candidate; $parents [$next] = $node; }
      }
    }
    foreach ( $overflow as $node => $unused )
      if ( ! is_finite ( $distance [$node] ) ) throw new InvalidArgumentException ( 'path length exceeds finite range' );
    $distances = []; foreach ( $graph ['ids'] as $i => $id ) $distances [] = [ 'id' => $id, 'distance' => is_finite ( $distance [$i] ) ? $distance [$i] : NULL, 'parent' => $parents [$i] === NULL ? NULL : $graph ['ids'] [$parents [$i]] ];
    $path = []; $status = 'ok';
    if ( isset ( $options ['target'] ) ) {
      $target = $graph ['index'] ['#' . $options ['target']];
      if ( ! is_finite ( $distance [$target] ) ) $status = 'unreachable';
      else for ( $node = $target; $node !== NULL; $node = $parents [$node] ) $path [] = $graph ['ids'] [$node];
    }
    return [ 'status' => $status, 'method' => 'Dijkstra; nonnegative weights', 'distances' => $distances, 'path' => array_reverse ( $path ) ];
  }

  function padGraphCriticalpath ( $graph, $options ) {
    [ $order, $cycle ] = padGraphOrder ( $graph );
    if ( $cycle ) return [ 'status' => 'cycle', 'cycle' => array_map ( fn ( $i ) => $graph ['ids'] [$i], $cycle ), 'duration' => NULL, 'tasks' => [], 'path' => [] ];
    $n = count ( $graph ['ids'] ); $early = $finish = array_fill ( 0, $n, 0.0 ); $parent = array_fill ( 0, $n, NULL );
    foreach ( $order as $node ) {
      $finish [$node] = $early [$node] + $graph ['durations'] [$node];
      if ( ! is_finite ( $finish [$node] ) ) throw new InvalidArgumentException ( 'critical path duration exceeds finite range' );
      foreach ( $graph ['adj'] [$node] as [ $next ] ) if ( $finish [$node] > $early [$next] ) { $early [$next] = $finish [$node]; $parent [$next] = $node; }
    }
    $duration = $n ? max ( $finish ) : 0.0; $late = array_fill ( 0, $n, $duration );
    foreach ( array_reverse ( $order ) as $node ) {
      foreach ( $graph ['adj'] [$node] as [ $next ] ) $late [$node] = min ( $late [$node], $late [$next] );
      $late [$node] -= $graph ['durations'] [$node];
    }
    $tasks = []; $tolerance = max ( 1.0, $duration ) * 1e-10;
    foreach ( $graph ['ids'] as $i => $id ) $tasks [] = [ 'id' => $id, 'duration' => $graph ['durations'] [$i], 'earliestStart' => $early [$i], 'earliestFinish' => $finish [$i],
      'latestStart' => $late [$i], 'slack' => max ( 0.0, $late [$i] - $early [$i] ), 'critical' => abs ( $late [$i] - $early [$i] ) <= $tolerance ];
    $path = [];
    if ( $n ) for ( $node = array_search ( $duration, $finish, TRUE ); $node !== NULL; $node = $parent [$node] ) $path [] = $graph ['ids'] [$node];
    return [ 'status' => 'ok', 'duration' => $duration, 'tasks' => $tasks, 'path' => array_reverse ( $path ), 'tolerance' => $tolerance ];
  }
?>
