<?php
  include_once PAD . 'subs/intervals/_lib/intervals.php';

  function padSeriesNumber ( $value ) {
    if ( ( ! is_int ( $value ) && ! is_float ( $value ) ) || ! is_finite ( (float) $value ) ) throw new InvalidArgumentException ( 'expected a finite native number' );
    return (float) $value;
  }

  function padSeriesAggregate ( $values, $method ) {
    if ( ! $values ) return NULL;
    if ( $method === 'min' ) return min ( $values );
    if ( $method === 'max' ) return max ( $values );
    // Sum before dividing when safe, preserving subnormal observations. Only
    // pre-divide very large means, whose otherwise finite answer could overflow.
    $count = count ( $values );
    $preDivide = $method === 'mean' && max ( array_map ( 'abs', $values ) ) > PHP_FLOAT_MAX / $count;
    $sum = 0.0; $comp = 0.0; $divisor = $preDivide ? $count : 1;
    foreach ( $values as $v ) {
      $y = $v / $divisor - $comp; $next = $sum + $y; $comp = ( $next - $sum ) - $y; $sum = $next;
      if ( ! is_finite ( $sum ) || ! is_finite ( $comp ) ) throw new InvalidArgumentException ( 'series arithmetic exceeds finite range' );
    }
    return $method === 'mean' && ! $preDivide ? $sum / $count : $sum;
  }

  function padSeriesBucketIndex ( $time, $origin, $step ) {
    $quotient = ( $time - $origin ) / $step;
    // Decimal fractions such as 0.3/0.1 can round just below an integer.
    // Snap only within the rounding uncertainty of these finite operands.
    $tolerance = 4 * PHP_FLOAT_EPSILON * max ( 1, abs ( $quotient ), abs ( $time / $step ), abs ( $origin / $step ) );
    if ( ! is_finite ( $quotient ) || ! is_finite ( $tolerance ) || $tolerance >= .25 ) throw new InvalidArgumentException ( 'bucket index exceeds reliable numeric precision' );
    $nearest = round ( $quotient );
    return (int) floor ( abs ( $quotient - $nearest ) <= $tolerance ? $nearest : $quotient );
  }

  function padSeriesPrepare ( $rows, $options ) {
    if ( ! is_array ( $rows ) || ! array_is_list ( $rows ) || count ( $rows ) > 100000 ) throw new InvalidArgumentException ( 'expected at most 100000 rows' );
    $time = $options ['time'] ?? 'time'; $value = $options ['value'] ?? 'value'; $series = $options ['series'] ?? NULL;
    foreach ( [ $time, $value ] as $field ) if ( ! is_string ( $field ) || $field === '' || strlen ( $field ) > 128 ) throw new InvalidArgumentException ( 'time/value must name fields' );
    if ( $series !== NULL && ( ! is_string ( $series ) || $series === '' || strlen ( $series ) > 128 ) ) throw new InvalidArgumentException ( 'series must name a field' );
    $duplicates = $options ['duplicates'] ?? 'error'; $missing = $options ['missing'] ?? 'keep';
    if ( ! in_array ( $duplicates, [ 'error', 'last', 'mean' ], TRUE ) || ! in_array ( $missing, [ 'keep', 'error' ], TRUE ) ) throw new InvalidArgumentException ( 'invalid duplicate or missing policy' );
    $groups = []; $mode = NULL; $missingCount = 0; $duplicateCount = 0;
    foreach ( $rows as $i => $row ) {
      if ( ! is_array ( $row ) || ! array_key_exists ( $time, $row ) || ( $series !== NULL && ! array_key_exists ( $series, $row ) ) ) throw new InvalidArgumentException ( 'row is missing its time or series field' );
      $t = $row [$time]; $nextMode = is_int ( $t ) || is_float ( $t ) ? 'number' : 'instant';
      if ( $mode !== NULL && $mode !== $nextMode ) throw new InvalidArgumentException ( 'do not mix numeric times and ISO instants' );
      $mode = $nextMode; $t = $mode === 'number' ? padSeriesNumber ( $t ) : padIntervalInstant ( $t );
      if ( $t < -62167219200 || $t > 253402300799 ) throw new InvalidArgumentException ( 'time is outside years 0000..9999' );
      $v = $row [$value] ?? NULL;
      if ( $v === NULL ) { $missingCount++; if ( $missing === 'error' ) throw new InvalidArgumentException ( 'missing observation' ); }
      else $v = padSeriesNumber ( $v );
      $label = $series === NULL ? '' : $row [$series];
      if ( ( ! is_int ( $label ) && ! is_string ( $label ) ) || strlen ( (string) $label ) > 256 || ( is_string ( $label ) && ! preg_match ( '//u', $label ) ) ) throw new InvalidArgumentException ( 'series IDs must be bounded UTF-8 strings or integers' );
      $key = ( is_int ( $label ) ? 'i:' : 's:' ) . $label;
      $groups [$key] ??= [ 'id' => $label, 'rows' => [] ];
      $groups [$key] ['rows'] [] = [ 'time' => $t, 'value' => $v, 'index' => $i ];
    }
    foreach ( $groups as &$group ) {
      usort ( $group ['rows'], fn ( $a, $b ) => [ $a ['time'], $a ['index'] ] <=> [ $b ['time'], $b ['index'] ] );
      $out = [];
      for ( $i = 0, $n = count ( $group ['rows'] ); $i < $n; ) {
        $first = $group ['rows'] [$i]; $batch = [ $first ]; $i++;
        while ( $i < $n && $group ['rows'] [$i] ['time'] == $first ['time'] ) $batch [] = $group ['rows'] [$i++];
        if ( count ( $batch ) > 1 && $duplicates === 'error' ) throw new InvalidArgumentException ( 'duplicate timestamp within series' );
        $duplicateCount += count ( $batch ) - 1;
        $chosen = $duplicates === 'last' ? end ( $batch ) : $first;
        if ( $duplicates === 'mean' ) $chosen ['value'] = padSeriesAggregate ( array_values ( array_filter ( array_column ( $batch, 'value' ), fn ( $v ) => $v !== NULL ) ), 'mean' );
        $out [] = $chosen;
      }
      $group ['rows'] = $out;
    }
    return [ $groups, $mode ?? 'number', $missingCount, $duplicateCount ];
  }

  function padSeriesCompute ( $kind, $rows, $options ) {
    $specific = match ( $kind ) { 'resample' => ['stepSeconds','origin','aggregate'], 'rolling' => ['window','minObservations','aggregate'], 'ewma' => ['alpha','gap'], 'difference' => ['lag','rate'] };
    if ( array_diff ( array_keys ( $options ), [ 'time', 'value', 'series', 'duplicates', 'missing', 'timezone', ...$specific ] ) ) throw new InvalidArgumentException ( 'unknown series option' );
    $zone = $options ['timezone'] ?? 'UTC';
    if ( ! is_string ( $zone ) || ! in_array ( $zone, DateTimeZone::listIdentifiers ( DateTimeZone::ALL_WITH_BC ), TRUE ) ) throw new InvalidArgumentException ( 'invalid IANA timezone' );
    [ $groups, $mode, $missing, $duplicates ] = padSeriesPrepare ( $rows, $options ); $tz = new DateTimeZone ( $zone );
    $aggregate = $options ['aggregate'] ?? 'mean';
    if ( ! in_array ( $aggregate, [ 'mean', 'sum', 'min', 'max' ], TRUE ) ) throw new InvalidArgumentException ( 'aggregate must be mean, sum, min or max' );
    $out = [];
    $emit = function ( $time, $value, $id, $count ) use ( &$out, $mode, $tz ) {
      if ( count ( $out ) >= 100000 ) throw new InvalidArgumentException ( 'series output exceeds 100000 rows' );
      if ( $value !== NULL && ! is_finite ( (float) $value ) ) throw new InvalidArgumentException ( 'series arithmetic exceeds finite range' );
      $out [] = [ 'time' => $mode === 'instant' ? ( new DateTimeImmutable ( '@' . (int) $time ) )->setTimezone ( $tz )->format ( 'Y-m-d\TH:i:sP' ) : $time, 'value' => $value, 'series' => $id, 'count' => $count ];
    };
    // Validate even empty input so malformed author options cannot hide behind no data.
    if ( $kind === 'resample' ) {
      $step = padSeriesNumber ( $options ['stepSeconds'] ?? 3600 ); $origin = padSeriesNumber ( $options ['origin'] ?? 0 );
      if ( $step <= 0 || $step > 315576000000 || abs ( $origin ) > 253402300799 || ( $mode === 'instant' && ( floor ( $step ) !== $step || floor ( $origin ) !== $origin ) ) ) throw new InvalidArgumentException ( 'positive fixed-second step required; instant buckets use whole seconds' );
    } elseif ( $kind === 'rolling' ) {
      $window = $options ['window'] ?? 5; $minimum = $options ['minObservations'] ?? $window;
      if ( ! is_int ( $window ) || $window < 1 || $window > 10000 || ! is_int ( $minimum ) || $minimum < 1 || $minimum > $window ) throw new InvalidArgumentException ( 'window is 1..10000, minObservations is 1..window' );
      if ( count ( $rows ) * $window > 10000000 ) throw new InvalidArgumentException ( 'rolling work budget exceeded' );
    } elseif ( $kind === 'ewma' ) {
      $alpha = padSeriesNumber ( $options ['alpha'] ?? .3 ); $gap = $options ['gap'] ?? 'reset';
      if ( $alpha <= 0 || $alpha > 1 || ! in_array ( $gap, [ 'reset', 'carry' ], TRUE ) ) throw new InvalidArgumentException ( 'alpha is (0,1], gap is reset or carry' );
    } else {
      $lag = $options ['lag'] ?? 1; $rate = $options ['rate'] ?? FALSE;
      if ( ! is_int ( $lag ) || $lag < 1 || $lag > 10000 || ! is_bool ( $rate ) ) throw new InvalidArgumentException ( 'lag is 1..10000; rate must be boolean' );
    }
    foreach ( $groups as $group ) {
      $points = $group ['rows']; $id = $group ['id'];
      if ( $kind === 'resample' ) {
        $buckets = [];
        foreach ( $points as $p ) {
          $idx = padSeriesBucketIndex ( $p ['time'], $origin, $step );
          $buckets [$idx] ??= []; if ( $p ['value'] !== NULL ) $buckets [$idx] [] = $p ['value'];
        }
        if ( ! $buckets ) continue;
        $first = min ( array_keys ( $buckets ) ); $last = max ( array_keys ( $buckets ) );
        if ( $last - $first + 1 > 100000 - count ( $out ) ) throw new InvalidArgumentException ( 'resample bucket budget exceeded' );
        for ( $i = $first; $i <= $last; $i++ ) {
          $time = $origin + $i * $step;
          if ( $origin + ( $i + 1 ) * $step <= $time || $time < -62167219200 || $time > 253402300799 ) throw new InvalidArgumentException ( 'bucket boundaries cannot be represented' );
          $values = $buckets [$i] ?? []; $emit ( $time, padSeriesAggregate ( $values, $aggregate ), $id, count ( $values ) );
        }
      } elseif ( $kind === 'rolling' ) {
        foreach ( $points as $i => $p ) {
          $values = array_values ( array_filter ( array_column ( array_slice ( $points, max ( 0, $i - $window + 1 ), min ( $window, $i + 1 ) ), 'value' ), fn ( $v ) => $v !== NULL ) );
          $v = $p ['value'] !== NULL && count ( $values ) >= $minimum ? padSeriesAggregate ( $values, $aggregate ) : NULL;
          $emit ( $p ['time'], $v, $id, count ( $values ) );
        }
      } elseif ( $kind === 'ewma' ) {
        $state = NULL;
        foreach ( $points as $p ) {
          if ( $p ['value'] === NULL ) { if ( $gap === 'reset' ) $state = NULL; $emit ( $p ['time'], NULL, $id, 0 ); continue; }
          $state = $state === NULL ? $p ['value'] : ( 1 - $alpha ) * $state + $alpha * $p ['value'];
          $emit ( $p ['time'], $state, $id, 1 );
        }
      } else foreach ( $points as $i => $p ) {
        $before = $points [$i - $lag] ?? NULL; $v = NULL;
        if ( $p ['value'] !== NULL && $before !== NULL && $before ['value'] !== NULL ) {
          $v = $p ['value'] - $before ['value'];
          if ( $rate ) $v /= $p ['time'] - $before ['time'];
        }
        $emit ( $p ['time'], $v, $id, $v === NULL ? 0 : 2 );
      }
    }
    $parameters = match ( $kind ) {
      'resample' => ['stepSeconds'=>$step,'origin'=>$origin,'aggregate'=>$aggregate,'boundaryPolicy'=>'snap-4-scaled-epsilons'],
      'rolling' => ['window'=>$window,'minObservations'=>$minimum,'aggregate'=>$aggregate],
      'ewma' => ['alpha'=>$alpha,'gap'=>$gap],
      'difference' => ['lag'=>$lag,'rate'=>$rate]
    };
    return [ 'rows' => $out, 'method' => $kind, 'timeMode' => $mode, 'timezone' => $zone, 'inputCount' => count ( $rows ), 'missing' => $missing, 'duplicates' => $duplicates,
      'duplicatePolicy' => $options ['duplicates'] ?? 'error', 'missingPolicy' => $options ['missing'] ?? 'keep', 'parameters' => $parameters ];
  }
?>
