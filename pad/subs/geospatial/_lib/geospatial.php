<?php
  // RFC 7946 coordinate order, spherical mean-Earth radius in metres (IUGG).
  const padGeoRadius = 6371008.8;

  function padGeoPoint ( $point ) {
    if ( ! is_array ( $point ) or ! array_is_list ( $point ) or count ( $point ) !== 2 )
      throw new InvalidArgumentException ( 'a point must be [longitude,latitude]' );
    foreach ( $point as $i => $value )
      if ( ! is_numeric ( $value ) or ! is_finite ( (float) $value ) or abs ( (float) $value ) > ( $i ? 90 : 180 ) )
        throw new InvalidArgumentException ( 'coordinates exceed WGS84 longitude/latitude bounds' );
    return array_map ( 'floatval', $point );
  }

  function padGeoLongitude ( $longitude ) {
    return fmod ( fmod ( $longitude + 180, 360 ) + 360, 360 ) - 180;
  }

  function padGeoCross ( $a, $b, $c ) {
    return ( $b [0] - $a [0] ) * ( $c [1] - $a [1] ) - ( $b [1] - $a [1] ) * ( $c [0] - $a [0] );
  }

  function padGeoOnSegment ( $a, $b, $p ) {
    return abs ( padGeoCross ( $a, $b, $p ) ) < 1e-10
      and $p [0] >= min ( $a [0], $b [0] ) - 1e-10 and $p [0] <= max ( $a [0], $b [0] ) + 1e-10
      and $p [1] >= min ( $a [1], $b [1] ) - 1e-10 and $p [1] <= max ( $a [1], $b [1] ) + 1e-10;
  }

  function padGeoSegmentsMeet ( $a, $b, $c, $d ) {
    return padGeoOnSegment ( $a, $b, $c ) or padGeoOnSegment ( $a, $b, $d )
      or padGeoOnSegment ( $c, $d, $a ) or padGeoOnSegment ( $c, $d, $b )
      or ( ( padGeoCross ( $a, $b, $c ) > 0 ) !== ( padGeoCross ( $a, $b, $d ) > 0 )
        and ( padGeoCross ( $c, $d, $a ) > 0 ) !== ( padGeoCross ( $c, $d, $b ) > 0 ) );
  }

  // 0 outside, 1 interior, 2 boundary; straight edges in unwrapped lon/lat space.
  function padGeoInRing ( $point, $ring ) {
    $inside = FALSE;
    for ( $i = 1; $i < count ( $ring ); $i++ ) {
      $a = $ring [$i - 1]; $b = $ring [$i];
      if ( padGeoOnSegment ( $a, $b, $point ) ) return 2;
      if ( ( $a [1] > $point [1] ) !== ( $b [1] > $point [1] )
           and $point [0] < ( $b [0] - $a [0] ) * ( $point [1] - $a [1] ) / ( $b [1] - $a [1] ) + $a [0] )
        $inside = ! $inside;
    }
    return $inside ? 1 : 0;
  }

  function padGeoPolygon ( $polygon ) {
    if ( ! is_array ( $polygon ) or ( $polygon ['type'] ?? '' ) !== 'Polygon'
         or ! is_array ( $polygon ['coordinates'] ?? NULL ) or ! $polygon ['coordinates'] )
      throw new InvalidArgumentException ( 'contains expects a GeoJSON Polygon' );
    $raw = $polygon ['coordinates']; $total = 0;
    foreach ( $raw as $ring ) {
      if ( ! is_array ( $ring ) or ! array_is_list ( $ring ) or count ( $ring ) < 4 )
        throw new InvalidArgumentException ( 'polygon rings need at least four points including closure' );
      $total += count ( $ring );
    }
    if ( $total > 1000 ) throw new InvalidArgumentException ( 'polygon exceeds 1000 vertices' );
    $rings = []; $anchor = NULL;
    foreach ( $raw as $ring ) {
      $ring = array_map ( 'padGeoPoint', $ring );
      if ( $ring [0] !== $ring [count ( $ring ) - 1] ) throw new InvalidArgumentException ( 'polygon ring is not closed' );
      $unwrapped = []; $previous = $anchor ?? $ring [0][0];
      foreach ( $ring as [ $lon, $lat ] ) {
        if ( abs ( $lat ) === 90.0 ) throw new InvalidArgumentException ( 'polygon vertices at poles are unsupported' );
        $delta = padGeoLongitude ( $lon - $previous );
        if ( abs ( $delta ) === 180.0 ) throw new InvalidArgumentException ( 'ambiguous 180-degree polygon edge' );
        $lon = $previous + $delta; $unwrapped [] = [ $lon, $lat ]; $previous = $lon;
      }
      if ( abs ( $unwrapped [0][0] - $previous ) > 1e-9 ) throw new InvalidArgumentException ( 'pole-enclosing polygons are unsupported' );
      $anchor ??= $unwrapped [0][0];
      $n = count ( $unwrapped ) - 1; $area = 0;
      for ( $i = 0; $i < $n; $i++ ) {
        if ( $unwrapped [$i] === $unwrapped [$i + 1] ) throw new InvalidArgumentException ( 'zero-length polygon edge' );
        $area += $unwrapped [$i][0] * $unwrapped [$i + 1][1] - $unwrapped [$i + 1][0] * $unwrapped [$i][1];
        for ( $j = $i + 2; $j < $n; $j++ )
          if ( ! ( $i === 0 and $j === $n - 1 ) and padGeoSegmentsMeet ( $unwrapped [$i], $unwrapped [$i + 1], $unwrapped [$j], $unwrapped [$j + 1] ) )
            throw new InvalidArgumentException ( 'self-intersecting polygon ring' );
      }
      if ( abs ( $area ) < 1e-10 ) throw new InvalidArgumentException ( 'degenerate polygon ring' );
      $rings [] = $unwrapped;
    }
    for ( $i = 1; $i < count ( $rings ); $i++ ) {
      if ( padGeoInRing ( $rings [$i][0], $rings [0] ) !== 1 ) throw new InvalidArgumentException ( 'hole is not strictly inside outer ring' );
      for ( $j = 0; $j < $i; $j++ ) {
        if ( $j > 0 and ( padGeoInRing ( $rings [$i][0], $rings [$j] ) or padGeoInRing ( $rings [$j][0], $rings [$i] ) ) )
          throw new InvalidArgumentException ( 'overlapping polygon holes' );
        for ( $a = 1; $a < count ( $rings [$i] ); $a++ )
          for ( $b = 1; $b < count ( $rings [$j] ); $b++ )
            if ( padGeoSegmentsMeet ( $rings [$i][$a - 1], $rings [$i][$a], $rings [$j][$b - 1], $rings [$j][$b] ) )
              throw new InvalidArgumentException ( 'intersecting polygon rings' );
      }
    }
    return $rings;
  }
?>
