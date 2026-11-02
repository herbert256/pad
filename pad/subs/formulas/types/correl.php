<?php
  return [ 'group' => 'Statistical', 'args' => [ 2, 2 ], 'call' => function ( $args, $env ) {
    $sets = []; $sizes = [];
    foreach ( $args as $arg ) {
      if ( padFormulaIsError ( $arg ) ) return $arg;
      $values = [];
      if ( padFormulaIsRange ( $arg ) ) {
        $sizes [] = padFormulaRangeSize ( $arg );
        foreach ( $arg ['range'] as $r => $row )
          foreach ( $row as $c => $value ) {
            if ( padFormulaIsError ( $value ) ) return $value;
            if ( is_int ( $value ) or is_float ( $value ) ) $values [ $r * $arg ['w'] + $c ] = (float) $value;
          }
      } else {
        $sizes [] = 1;
        if ( is_int ( $arg ) or is_float ( $arg ) ) $values [0] = (float) $arg;
      }
      $sets [] = $values;
    }
    if ( $sizes [0] !== $sizes [1] ) return padFormulaError ( '#N/A' );
    $x = array_intersect_key ( $sets [0], $sets [1] );
    $y = array_intersect_key ( $sets [1], $sets [0] );
    if ( count ( $x ) < 2 ) return padFormulaError ( '#DIV/0!' );
    $scaleX = max ( array_map ( 'abs', $x ) ); $scaleY = max ( array_map ( 'abs', $y ) );
    if ( ! is_finite ( $scaleX ) or ! is_finite ( $scaleY ) ) return padFormulaError ( '#NUM!' );
    if ( $scaleX == 0 or $scaleY == 0 ) return padFormulaError ( '#DIV/0!' );
    $x = array_map ( fn ( $v ) => $v / $scaleX, $x ); $y = array_map ( fn ( $v ) => $v / $scaleY, $y );
    $mx = array_sum ( $x ) / count ( $x ); $my = array_sum ( $y ) / count ( $y );
    $xx = $yy = $xy = 0.0;
    foreach ( $x as $i => $value ) {
      $a = $value - $mx; $b = $y [$i] - $my;
      $xx += $a * $a; $yy += $b * $b; $xy += $a * $b;
    }
    if ( $xx <= 0 or $yy <= 0 ) return padFormulaError ( '#DIV/0!' );
    return max ( -1.0, min ( 1.0, $xy / sqrt ( $xx ) / sqrt ( $yy ) ) );
  } ];
?>
