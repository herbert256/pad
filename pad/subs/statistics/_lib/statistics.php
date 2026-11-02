<?php
  function padStatsFiniteResult ( $value ) {
    if ( is_float ( $value ) && ! is_finite ( $value ) ) throw new InvalidArgumentException ( 'result exceeds the finite numeric range' );
    if ( is_array ( $value ) ) foreach ( $value as $child ) padStatsFiniteResult ( $child );
  }

  function padStatsRows ( $rows, $options, $paired = FALSE ) {
    if ( ! is_array ( $rows ) || ! array_is_list ( $rows ) || count ( $rows ) > 100000 )
      throw new InvalidArgumentException ( 'expected a list of at most 100000 observations' );
    $missing = $options ['missing'] ?? 'omit';
    if ( ! in_array ( $missing, [ 'omit', 'error' ], TRUE ) ) throw new InvalidArgumentException ( 'missing must be omit or error' );
    foreach ( $paired ? [ 'x', 'y' ] : [ 'field' ] as $key )
      if ( isset ( $options [$key] ) && ! is_string ( $options [$key] ) && ! is_int ( $options [$key] ) )
        throw new InvalidArgumentException ( "$key must name a field" );
    $values = []; $indices = []; $omitted = 0;
    foreach ( $rows as $i => $row ) {
      if ( $paired ) {
        if ( ! is_array ( $row ) ) throw new InvalidArgumentException ( 'regression rows must contain x and y fields' );
        $observation = [ $row [$options ['x'] ?? 'x'] ?? NULL, $row [$options ['y'] ?? 'y'] ?? NULL ];
      } else {
        if ( isset ( $options ['field'] ) && ! is_array ( $row ) ) throw new InvalidArgumentException ( 'field requires record rows' );
        $observation = [ isset ( $options ['field'] ) ? ( $row [$options ['field']] ?? NULL ) : $row ];
      }
      $hasMissing = FALSE;
      foreach ( $observation as $value ) {
        if ( $value === NULL ) { $hasMissing = TRUE; continue; }
        if ( ( ! is_int ( $value ) && ! is_float ( $value ) ) || ! is_finite ( (float) $value ) )
          throw new InvalidArgumentException ( "observation $i must contain finite native numbers or null" );
      }
      if ( $hasMissing ) {
        if ( $missing === 'error' ) throw new InvalidArgumentException ( "missing observation $i" );
        $omitted++; continue;
      }
      $values [] = $paired ? array_map ( 'floatval', $observation ) : (float) $observation [0]; $indices [] = $i;
    }
    return [ $values, $indices, [ 'count' => count ( $values ), 'missing' => $omitted, 'missingPolicy' => $missing ] ];
  }

  // Compensated summation and a shifted origin retain small spreads on large offsets.
  function padStatsSum ( $values ) {
    $sum = 0.0; $c = 0.0;
    foreach ( $values as $value ) { $y = $value - $c; $next = $sum + $y; $c = ( $next - $sum ) - $y; $sum = $next; }
    if ( ! is_finite ( $sum ) ) throw new InvalidArgumentException ( 'numeric accumulation exceeds finite range' );
    return $sum;
  }

  function padStatsCenter ( $values ) {
    $origin = $values [0];
    $delta = array_map ( fn ( $v ) => $v - $origin, $values );
    $shift = padStatsSum ( array_map ( fn ( $v ) => $v / count ( $values ), $delta ) );
    return [ $origin + $shift, array_map ( fn ( $v ) => $v - $shift, $delta ) ];
  }

  // Linear interpolation at (n-1)*p, matching PAD chart quartiles (Hyndman-Fan type 7).
  function padStatsQuantile ( $sorted, $p ) {
    if ( ! $sorted ) return NULL;
    $at = ( count ( $sorted ) - 1 ) * $p; $low = (int) floor ( $at ); $high = (int) ceil ( $at ); $weight = $at - $low;
    return ( 1 - $weight ) * $sorted [$low] + $weight * $sorted [$high];
  }

  function padStatsDescribe ( $rows, $options ) {
    [ $values, , $meta ] = padStatsRows ( $rows, $options ); $n = count ( $values );
    $result = $meta + [ 'method' => 'sample moments; type-7 quartiles', 'sum' => 0.0, 'mean' => NULL, 'min' => NULL, 'max' => NULL,
      'median' => NULL, 'q1' => NULL, 'q3' => NULL, 'variance' => NULL, 'sd' => NULL ];
    if ( ! $n ) return $result;
    [ $mean, $deviations ] = padStatsCenter ( $values ); sort ( $values, SORT_NUMERIC );
    $scale = max ( array_map ( 'abs', $deviations ) );
    $sd = $n < 2 ? NULL : ( $scale == 0 ? 0.0 : sqrt ( padStatsSum ( array_map ( fn ( $d ) => ( $d / $scale ) ** 2, $deviations ) ) / ( $n - 1 ) ) * $scale );
    return array_replace ( $result, [ 'sum' => padStatsSum ( $values ), 'mean' => $mean, 'min' => $values [0], 'max' => end ( $values ),
      'median' => padStatsQuantile ( $values, .5 ), 'q1' => padStatsQuantile ( $values, .25 ), 'q3' => padStatsQuantile ( $values, .75 ),
      'variance' => $sd === NULL ? NULL : $sd * $sd, 'sd' => $sd ] );
  }

  function padStatsQuantiles ( $rows, $options ) {
    $probabilities = $options ['probabilities'] ?? [ 0, .25, .5, .75, 1 ];
    if ( ! is_array ( $probabilities ) || ! array_is_list ( $probabilities ) || count ( $probabilities ) > 1000 )
      throw new InvalidArgumentException ( 'probabilities must be a list of at most 1000 numbers in [0,1]' );
    foreach ( $probabilities as $p )
      if ( ( ! is_int ( $p ) && ! is_float ( $p ) ) || ! is_finite ( (float) $p ) || $p < 0 || $p > 1 )
        throw new InvalidArgumentException ( 'probabilities must be finite native numbers in [0,1]' );
    [ $values, , $meta ] = padStatsRows ( $rows, $options ); sort ( $values, SORT_NUMERIC );
    return $meta + [ 'method' => 'Hyndman-Fan type 7', 'quantiles' => array_map ( fn ( $p ) => [ 'probability' => $p, 'value' => padStatsQuantile ( $values, $p ) ], $probabilities ) ];
  }

  function padStatsHistogram ( $rows, $options ) {
    [ $values, , $meta ] = padStatsRows ( $rows, $options ); $bins = $options ['bins'] ?? 10;
    if ( ! is_int ( $bins ) || $bins < 1 || $bins > 1000 ) throw new InvalidArgumentException ( 'bins must be an integer from 1 to 1000' );
    $edges = $options ['edges'] ?? NULL;
    if ( $edges !== NULL ) {
      if ( ! is_array ( $edges ) || ! array_is_list ( $edges ) || count ( $edges ) < 2 || count ( $edges ) > 1001 )
        throw new InvalidArgumentException ( 'edges must contain 2 to 1001 strictly increasing numbers' );
      foreach ( $edges as $i => $edge )
        if ( ( ! is_int ( $edge ) && ! is_float ( $edge ) ) || ! is_finite ( (float) $edge ) || ( $i && $edge <= $edges [$i - 1] ) )
          throw new InvalidArgumentException ( 'edges must be finite and strictly increasing' );
    } elseif ( ! $values ) return $meta + [ 'method' => 'left-closed; final bin right-closed', 'bins' => [], 'underflow' => 0, 'overflow' => 0 ];
    else {
      $lo = min ( $values ); $hi = max ( $values );
      if ( $lo === $hi ) return $meta + [ 'method' => 'constant point mass', 'bins' => [ [ 'lower' => $lo, 'upper' => $hi, 'count' => count ( $values ) ] ], 'underflow' => 0, 'overflow' => 0 ];
      $edges = [];
      for ( $i = 0; $i <= $bins; $i++ ) $edges [] = ( 1 - $i / $bins ) * $lo + ( $i / $bins ) * $hi;
      for ( $i = 1; $i < count ( $edges ); $i++ )
        if ( $edges [$i] <= $edges [$i - 1] ) throw new InvalidArgumentException ( 'bin edges are indistinguishable at this numeric precision; reduce bins' );
    }
    $counts = array_fill ( 0, count ( $edges ) - 1, 0 ); $under = $over = 0; $last = count ( $edges ) - 1;
    foreach ( $values as $value ) {
      if ( $value < $edges [0] ) { $under++; continue; }
      if ( $value > $edges [$last] ) { $over++; continue; }
      $low = 0; $high = $last;
      while ( $low + 1 < $high ) { $mid = intdiv ( $low + $high, 2 ); if ( $value < $edges [$mid] ) $high = $mid; else $low = $mid; }
      $counts [min ( $last - 1, $low )]++;
    }
    $result = []; foreach ( $counts as $i => $count ) $result [] = [ 'lower' => $edges [$i], 'upper' => $edges [$i + 1], 'count' => $count ];
    return $meta + [ 'method' => 'left-closed; final bin right-closed', 'bins' => $result, 'underflow' => $under, 'overflow' => $over ];
  }

  function padStatsRegression ( $rows, $options ) {
    [ $pairs, , $meta ] = padStatsRows ( $rows, $options, TRUE );
    $result = $meta + [ 'method' => 'ordinary least squares with intercept', 'status' => 'degenerate', 'slope' => NULL, 'intercept' => NULL, 'r' => NULL, 'rSquared' => NULL ];
    if ( count ( $pairs ) < 2 ) return $result;
    [ $mx, $x ] = padStatsCenter ( array_column ( $pairs, 0 ) ); [ $my, $y ] = padStatsCenter ( array_column ( $pairs, 1 ) );
    $sx = max ( array_map ( 'abs', $x ) ); $sy = max ( array_map ( 'abs', $y ) );
    if ( $sx == 0 ) return $result;
    if ( $sy == 0 ) return array_replace ( $result, [ 'status' => 'ok', 'slope' => 0.0, 'intercept' => $my ] );
    $x = array_map ( fn ( $v ) => $v / $sx, $x ); $y = array_map ( fn ( $v ) => $v / $sy, $y );
    $xx = padStatsSum ( array_map ( fn ( $v ) => $v * $v, $x ) ); $yy = padStatsSum ( array_map ( fn ( $v ) => $v * $v, $y ) );
    $xy = padStatsSum ( array_map ( fn ( $a, $b ) => $a * $b, $x, $y ) );
    // Divide scales first when representable; otherwise apply the normalized
    // coefficient before rescaling, so a finite (including zero) slope survives.
    $ratio = $sy / $sx; $coefficient = $xy / $xx;
    $slope = is_finite ( $ratio ) && $ratio != 0 ? $coefficient * $ratio : $coefficient * $sy / $sx;
    $r = max ( -1.0, min ( 1.0, $xy / sqrt ( $xx ) / sqrt ( $yy ) ) );
    return array_replace ( $result, [ 'status' => 'ok', 'slope' => $slope, 'intercept' => $my - $slope * $mx, 'r' => $r, 'rSquared' => $r * $r ] );
  }

  function padStatsOutliers ( $rows, $options ) {
    $factor = $options ['factor'] ?? 1.5;
    if ( ( ! is_int ( $factor ) && ! is_float ( $factor ) ) || ! is_finite ( (float) $factor ) || $factor < 0 || $factor > 100 )
      throw new InvalidArgumentException ( 'factor must be a finite native number in [0,100]' );
    [ $values, $indices, $meta ] = padStatsRows ( $rows, $options ); $sorted = $values; sort ( $sorted, SORT_NUMERIC );
    $q1 = padStatsQuantile ( $sorted, .25 ); $q3 = padStatsQuantile ( $sorted, .75 );
    $lower = $values ? $q1 - $factor * ( $q3 - $q1 ) : NULL; $upper = $values ? $q3 + $factor * ( $q3 - $q1 ) : NULL;
    $outliers = []; foreach ( $values as $i => $value ) if ( $value < $lower || $value > $upper ) $outliers [] = [ 'index' => $indices [$i], 'value' => $value ];
    return $meta + [ 'method' => 'Tukey fences; type-7 quartiles', 'factor' => $factor, 'lower' => $lower, 'upper' => $upper, 'outliers' => $outliers ];
  }
?>
