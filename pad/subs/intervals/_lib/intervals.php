<?php

  function padIntervalInstant ( $value ) {
    if ( ! is_string ( $value ) || ! preg_match ( '/^(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2})(?::(\d{2}))?(Z|[+-]\d{2}:\d{2})$/D', $value, $m ) )
      throw new InvalidArgumentException ( 'bounds must be finite numbers or ISO instants with explicit offsets' );
    $offset = $m [4] === 'Z' ? '+00:00' : $m [4];
    if ( (int) substr ( $offset, 1, 2 ) > 14 || (int) substr ( $offset, 4, 2 ) > 59 || ( (int) substr ( $offset, 1, 2 ) === 14 && (int) substr ( $offset, 4, 2 ) !== 0 ) )
      throw new InvalidArgumentException ( 'invalid instant offset' );
    $canonical = $m [1] . 'T' . $m [2] . ':' . ( ( $m [3] ?? '' ) !== '' ? $m [3] : '00' ) . $offset;
    $date = DateTimeImmutable::createFromFormat ( '!Y-m-d\TH:i:sP', $canonical );
    if ( ! $date || $date->format ( 'Y-m-d\TH:i:sP' ) !== $canonical ) throw new InvalidArgumentException ( 'invalid instant' );
    return $date->getTimestamp ();
  }

  function padIntervalRows ( $rows, $group, &$mode ) {
    if ( ! is_array ( $rows ) || ! array_is_list ( $rows ) || count ( $rows ) > 10000 )
      throw new InvalidArgumentException ( 'each operand must be a list of at most 10000 intervals' );
    $groups = [];
    foreach ( $rows as $i => $row ) {
      if ( ! is_array ( $row ) || ! array_key_exists ( 'start', $row ) || ! array_key_exists ( 'end', $row ) || array_diff ( array_keys ( $row ), [ 'start', 'end', 'id', $group ] ) )
        throw new InvalidArgumentException ( 'interval records contain start, end, optional id and the grouping field' );
      $bounds = [];
      foreach ( [ 'start', 'end' ] as $field ) {
        $v = $row [$field]; $thisMode = is_int ( $v ) || is_float ( $v ) ? 'number' : 'instant';
        if ( $mode !== NULL && $mode !== $thisMode ) throw new InvalidArgumentException ( 'numeric and instant bounds cannot be mixed' );
        $mode = $thisMode;
        if ( $mode === 'number' ) { if ( ! is_finite ( (float) $v ) ) throw new InvalidArgumentException ( 'nonfinite bound' ); }
        else $v = padIntervalInstant ( $v );
        $bounds [] = $v;
      }
      [ $a, $b ] = $bounds;
      if ( $a > $b ) throw new InvalidArgumentException ( 'end precedes start' );
      $key = array_key_exists ( $group, $row ) ? $row [$group] : ''; $id = array_key_exists ( 'id', $row ) ? $row ['id'] : $i;
      foreach ( [ $key, $id ] as $value ) if ( ( ! is_string ( $value ) && ! is_int ( $value ) ) || strlen ( (string) $value ) > 256 )
        throw new InvalidArgumentException ( 'group and id must be strings or integers of at most 256 bytes' );
      if ( is_string ( $key ) && ! preg_match ( '//u', $key ) || is_string ( $id ) && ! preg_match ( '//u', $id ) ) throw new InvalidArgumentException ( 'invalid UTF-8 identifier' );
      if ( $a === $b || $a == $b ) continue; // Half-open empty intervals contribute no coverage.
      $hash = ( is_int ( $key ) ? 'i:' : 's:' ) . $key;
      $groups [$hash] ??= [ 'key' => $key, 'rows' => [] ];
      $groups [$hash] ['rows'] [] = [ $a, $b, $i, $id ];
    }
    foreach ( $groups as &$set ) usort ( $set ['rows'], fn ( $a, $b ) => [ $a [0], $a [1], $a [2] ] <=> [ $b [0], $b [1], $b [2] ] );
    return $groups;
  }

  function padIntervalUnionRanges ( $rows ) {
    usort ( $rows, fn ( $a, $b ) => [ $a [0], $a [1] ] <=> [ $b [0], $b [1] ] );
    $out = [];
    foreach ( $rows as $row ) {
      $i = count ( $out ) - 1;
      if ( $i >= 0 && $row [0] <= $out [$i] [1] ) $out [$i] [1] = max ( $out [$i] [1], $row [1] );
      else $out [] = [ $row [0], $row [1] ];
    }
    return $out;
  }

  function padIntervalCompute ( $kind, $left, $right, $options ) {
    if ( array_diff ( array_keys ( $options ), [ 'group', 'timezone', 'maxResults' ] ) ) throw new InvalidArgumentException ( 'unknown interval option' );
    $group = $options ['group'] ?? 'resource'; $zone = $options ['timezone'] ?? 'UTC'; $limit = $options ['maxResults'] ?? 10000;
    if ( ! is_string ( $group ) || $group === '' || strlen ( $group ) > 128 || in_array ( $group, [ 'start', 'end', 'id', 'left', 'right' ], TRUE ) ) throw new InvalidArgumentException ( 'invalid or reserved grouping field' );
    if ( ! is_string ( $zone ) || ! in_array ( $zone, DateTimeZone::listIdentifiers ( DateTimeZone::ALL_WITH_BC ), TRUE ) ) throw new InvalidArgumentException ( 'timezone must be an IANA name' );
    if ( ! is_int ( $limit ) || $limit < 1 || $limit > 100000 ) throw new InvalidArgumentException ( 'maxResults must be 1..100000' );
    $mode = NULL; $a = padIntervalRows ( $left, $group, $mode ); $b = padIntervalRows ( $right, $group, $mode ); $out = []; $work = 0;
    $tz = new DateTimeZone ( $zone );
    $stamp = fn ( $v ) => $mode === 'instant' ? ( new DateTimeImmutable ( '@' . $v ) )->setTimezone ( $tz )->format ( 'Y-m-d\TH:i:sP' ) : $v;
    $emit = function ( $from, $to, $key, $extra = [] ) use ( &$out, $limit, $stamp, $group ) {
      if ( count ( $out ) >= $limit ) throw new InvalidArgumentException ( 'interval output exceeds maxResults' );
      $out [] = [ 'start' => $stamp ( $from ), 'end' => $stamp ( $to ), $group => $key ] + $extra;
    };
    $keys = $kind === 'union' ? array_unique ( array_merge ( array_keys ( $a ), array_keys ( $b ) ) ) : array_keys ( $a );
    foreach ( $keys as $key ) {
      $x = $a [$key] ['rows'] ?? []; $y = $b [$key] ['rows'] ?? []; $label = $a [$key] ['key'] ?? $b [$key] ['key'];
      if ( $kind === 'union' ) { foreach ( padIntervalUnionRanges ( array_merge ( $x, $y ) ) as [ $lo, $hi ] ) $emit ( $lo, $hi, $label ); continue; }
      if ( $kind === 'overlaps' ) {
        foreach ( $x as $one ) foreach ( $y as $two ) {
          if ( ++$work > 10000000 ) throw new InvalidArgumentException ( 'overlap work budget exceeded' );
          if ( $two [0] >= $one [1] ) break;
          if ( $two [1] > $one [0] ) $emit ( max ( $one [0], $two [0] ), min ( $one [1], $two [1] ), $label,
            [ 'left' => [ 'index' => $one [2], 'id' => $one [3] ], 'right' => [ 'index' => $two [2], 'id' => $two [3] ] ] );
        }
        continue;
      }
      $x = padIntervalUnionRanges ( $x ); $y = padIntervalUnionRanges ( $y ); $j = 0;
      foreach ( $x as [ $lo, $hi ] ) {
        while ( isset ( $y [$j] ) && $y [$j] [1] <= $lo ) $j++;
        $cursor = $lo;
        for ( $k = $j; isset ( $y [$k] ) && $y [$k] [0] < $hi; $k++ ) {
          if ( $kind === 'intersection' ) $emit ( max ( $lo, $y [$k] [0] ), min ( $hi, $y [$k] [1] ), $label );
          elseif ( $y [$k] [0] > $cursor ) $emit ( $cursor, min ( $hi, $y [$k] [0] ), $label );
          $cursor = max ( $cursor, $y [$k] [1] );
          if ( $cursor >= $hi ) break;
        }
        if ( $kind === 'subtract' && $cursor < $hi ) $emit ( $cursor, $hi, $label );
      }
    }
    return $out;
  }

?>
