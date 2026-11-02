<?php
  function padGeometryScalar ( $v ) {
    if ( ( ! is_int ( $v ) && ! is_float ( $v ) ) || ! is_finite ( (float) $v ) || abs ( $v ) > 1e100 ) throw new InvalidArgumentException ( 'coordinates must be finite native numbers with magnitude <= 1e100' );
    return (float) $v;
  }
  function padGeometryPoints ( $points, $limit ) {
    if ( ! is_array ( $points ) || ! array_is_list ( $points ) || count ( $points ) > $limit ) throw new InvalidArgumentException ( 'point list exceeds its budget' );
    $out = [];
    foreach ( $points as $point ) {
      if ( ! is_array ( $point ) || ! array_is_list ( $point ) || count ( $point ) !== 2 ) throw new InvalidArgumentException ( 'each point is [x,y]; holes and nested geometries are unsupported' );
      $out [] = array_map ( 'padGeometryScalar', $point );
    }
    return $out;
  }
  function padGeometryFrame ( $points ) {
    $origin = $points [0] ?? [0.0,0.0]; $scale = 0.0;
    foreach ( $points as $p ) $scale = max ( $scale, abs ( $p [0] - $origin [0] ), abs ( $p [1] - $origin [1] ) );
    $normal = $scale == 0 ? array_fill ( 0, count ( $points ), [0.0,0.0] ) : array_map ( fn ( $p ) => [ ( $p [0] - $origin [0] ) / $scale, ( $p [1] - $origin [1] ) / $scale ], $points );
    return [ $normal, $origin, $scale ];
  }
  function padGeometryCross ( $a, $b, $c ) { return ( $b [0] - $a [0] ) * ( $c [1] - $a [1] ) - ( $b [1] - $a [1] ) * ( $c [0] - $a [0] ); }
  function padGeometryOn ( $a, $b, $p, $eps ) { return abs ( padGeometryCross ( $a, $b, $p ) ) <= $eps && $p [0] >= min ( $a [0], $b [0] ) - $eps && $p [0] <= max ( $a [0], $b [0] ) + $eps && $p [1] >= min ( $a [1], $b [1] ) - $eps && $p [1] <= max ( $a [1], $b [1] ) + $eps; }
  function padGeometrySegments ( $a, $b, $c, $d, $eps ) {
    $abC = padGeometryCross ( $a, $b, $c ); $abD = padGeometryCross ( $a, $b, $d ); $cdA = padGeometryCross ( $c, $d, $a ); $cdB = padGeometryCross ( $c, $d, $b );
    if ( ( $abC > $eps && $abD < -$eps || $abC < -$eps && $abD > $eps ) && ( $cdA > $eps && $cdB < -$eps || $cdA < -$eps && $cdB > $eps ) ) return TRUE;
    return padGeometryOn ( $a, $b, $c, $eps ) || padGeometryOn ( $a, $b, $d, $eps ) || padGeometryOn ( $c, $d, $a, $eps ) || padGeometryOn ( $c, $d, $b, $eps );
  }
  function padGeometryRing ( $points, $eps ) {
    if ( count ( $points ) > 1 && $points [0] === end ( $points ) ) array_pop ( $points );
    if ( count ( $points ) < 3 ) throw new InvalidArgumentException ( 'a ring needs at least three vertices' );
    [ $normal ] = padGeometryFrame ( $points ); $n = count ( $points );
    for ( $i = 0; $i < $n; $i++ ) {
      $next = ( $i + 1 ) % $n;
      if ( $points [$i] === $points [$next] ) throw new InvalidArgumentException ( 'consecutive duplicate vertices' );
      for ( $j = $i + 1; $j < $n; $j++ ) {
        $after = ( $j + 1 ) % $n;
        if ( $j === $next || $after === $i ) continue;
        if ( padGeometrySegments ( $normal [$i], $normal [$next], $normal [$j], $normal [$after], $eps ) ) throw new InvalidArgumentException ( 'self-intersection, touching nonadjacent edges or numerically ambiguous ring' );
      }
    }
    return $points;
  }
  function padGeometryMetrics ( $points, $eps ) {
    [ $p, $origin, $scale ] = padGeometryFrame ( $points ); $twice = 0.0; $comp = 0.0; $cx = 0.0; $cy = 0.0; $n = count ( $p );
    for ( $i = 0; $i < $n; $i++ ) {
      $j = ( $i + 1 ) % $n; $cross = $p [$i] [0] * $p [$j] [1] - $p [$j] [0] * $p [$i] [1];
      $y = $cross - $comp; $sum = $twice + $y; $comp = ( $sum - $twice ) - $y; $twice = $sum;
      $cx += ( $p [$i] [0] + $p [$j] [0] ) * $cross; $cy += ( $p [$i] [1] + $p [$j] [1] ) * $cross;
    }
    if ( abs ( $twice ) <= $eps ) return ['status'=>'degenerate','area'=>0.0,'signedArea'=>0.0,'centroid'=>NULL,'orientation'=>'none'];
    $signed = $twice / 2 * $scale * $scale;
    if ( ! is_finite ( $signed ) || $signed == 0 ) throw new InvalidArgumentException ( 'area overflow or underflow' );
    $center = [ $origin [0] + $cx / ( 3 * $twice ) * $scale, $origin [1] + $cy / ( 3 * $twice ) * $scale ];
    foreach ( $center as $v ) if ( ! is_finite ( $v ) ) throw new InvalidArgumentException ( 'centroid overflow' );
    return ['status'=>'ok','area'=>abs($signed),'signedArea'=>$signed,'centroid'=>$center,'orientation'=>$signed>0?'counterclockwise':'clockwise'];
  }
  function padGeometryHull ( $points, $eps ) {
    usort ( $points, fn ( $a, $b ) => $a <=> $b ); $unique = [];
    foreach ( $points as $p ) if ( ! $unique || end ( $unique ) !== $p ) $unique [] = $p;
    if ( count ( $unique ) < 3 ) return $unique;
    [ $normal ] = padGeometryFrame ( $unique ); $parts = [];
    foreach ( [ range ( 0, count ( $unique ) - 1 ), range ( count ( $unique ) - 1, 0 ) ] as $order ) {
      $chain = [];
      foreach ( $order as $i ) {
        while ( count ( $chain ) >= 2 && padGeometryCross ( $normal [$chain [count($chain)-2]], $normal [end($chain)], $normal [$i] ) <= $eps ) array_pop ( $chain );
        $chain [] = $i;
      }
      array_pop ( $chain ); $parts = array_merge ( $parts, $chain );
    }
    return array_map ( fn ( $i ) => $unique [$i], $parts );
  }
  function padGeometryClip ( $points, $box, $eps ) {
    if ( ! is_array ( $box ) || ! array_is_list ( $box ) || count ( $box ) !== 4 ) throw new InvalidArgumentException ( 'clip needs box=[minX,minY,maxX,maxY]' );
    $box = array_map ( 'padGeometryScalar', $box );
    if ( $box [0] > $box [2] || $box [1] > $box [3] ) throw new InvalidArgumentException ( 'reversed clipping bounds' );
    [ $normal ] = padGeometryFrame ( $points ); $sign = 0;
    foreach ( $normal as $i => $p ) {
      $turn = padGeometryCross ( $p, $normal [($i+1)%count($normal)], $normal [($i+2)%count($normal)] );
      if ( abs ( $turn ) > $eps ) { $s = $turn > 0 ? 1 : -1; if ( $sign && $s !== $sign ) throw new InvalidArgumentException ( 'rectangle clipping currently requires a convex polygon' ); $sign = $s; }
    }
    if ( ! $sign || $box [0] == $box [2] || $box [1] == $box [3] ) return [];
    foreach ( [ [0,$box[0],1], [0,$box[2],-1], [1,$box[1],1], [1,$box[3],-1] ] as [$axis,$edge,$direction] ) {
      $out = [];
      if ( ! $points ) return [];
      $previous = end ( $points ); $wasInside = ( $previous [$axis] - $edge ) * $direction >= 0;
      foreach ( $points as $p ) {
        $inside = ( $p [$axis] - $edge ) * $direction >= 0;
        if ( $inside !== $wasInside ) {
          $t = ( $edge - $previous [$axis] ) / ( $p [$axis] - $previous [$axis] ); $other = 1 - $axis;
          $cross = [0.0,0.0]; $cross [$axis] = $edge; $cross [$other] = $previous [$other] + $t * ( $p [$other] - $previous [$other] ); $out [] = $cross;
        }
        if ( $inside ) $out [] = $p;
        $previous = $p; $wasInside = $inside;
      }
      $points = [];
      foreach ( $out as $p ) if ( ! $points || end ( $points ) !== $p ) $points [] = $p;
      if ( count ( $points ) > 1 && $points [0] === end ( $points ) ) array_pop ( $points );
    }
    return count ( $points ) < 3 || padGeometryMetrics ( $points, $eps ) ['status'] !== 'ok' ? [] : $points;
  }
  function padGeometryCompute ( $kind, $points, $options ) {
    if ( array_diff ( array_keys ( $options ), $kind === 'clip' ? ['tolerance','box'] : ['tolerance'] ) ) throw new InvalidArgumentException ( 'unknown geometry option' );
    $eps = $options ['tolerance'] ?? 1e-12;
    if ( ( ! is_int ( $eps ) && ! is_float ( $eps ) ) || ! is_finite ( (float) $eps ) || $eps < 0 || $eps > 1e-3 ) throw new InvalidArgumentException ( 'dimensionless tolerance is 0..0.001' );
    $points = padGeometryPoints ( $points, $kind === 'hull' ? 10000 : 2000 );
    if ( $kind === 'hull' ) { $hull = padGeometryHull ( $points, $eps ); return ['status'=>count($hull)<3?'degenerate':'ok','points'=>$hull,'tolerance'=>$eps]; }
    $points = padGeometryRing ( $points, $eps );
    if ( $kind === 'clip' ) { $out = padGeometryClip ( $points, $options ['box'] ?? NULL, $eps ); return ['status'=>$out?'ok':'empty','points'=>$out,'tolerance'=>$eps]; }
    $metrics = padGeometryMetrics ( $points, $eps );
    if ( $kind === 'area' ) unset ( $metrics ['centroid'] );
    return $metrics + ['vertices'=>count($points),'tolerance'=>$eps];
  }
?>
