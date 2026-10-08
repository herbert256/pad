<?php

  // How two or three sets overlap: a circle per set, its area its size, the overlaps as
  // large as the data says.
  //
  //   {chart 'venn', data='speakers', sets='sets', value='people'}
  //
  // A row names one set - 'English' - or the overlap of two or three - 'English&Spanish',
  // or 'English, Spanish' - in sets=, else the first field that is no number, and its size
  // in value=, else the first numeric field. A set's size counts everyone in it, the overlaps
  // included, as an overlap's counts everyone in all its sets. Every set needs a row of its
  // own; an overlap without a row is empty.
  //
  // Two circles are set apart so their lens has the area of the overlap, found by halving
  // the distance; three stand at the distances of their three pairs, which makes the area
  // of each pair right and the middle near. The circles are light fills in the colours of
  // the slots, named outside; every region with room shows the count of that region alone
  // - in this set and that one, but not the third.

  function padChartVenn ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used  = [];
    $value = padChartPick ( 'value', $numbers, $used );
    $field = padChartPick ( 'sets',  $names,   $used );
    $sets  = $sizes = $desc = [];

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $v   = $row [$value] ?? NULL;

      if ( ! padChartFinite ( $v ) or $v < 0 )
        continue;

      $members = array_values ( array_unique ( padChartFields ( str_replace ( '&', ',', padChartText ( $row, $field ) ) ) ) );

      if ( ! $members )
        continue;

      if ( count ( $members ) == 1 and ! in_array ( $members [0], $sets, TRUE ) )
        $sets [] = $members [0];

      $sizes [] = [ $members, $v + 0 ];
      $desc  [] = implode ( ' & ', $members ) . ': ' . padChartNumber ( $v + 0 );

    }

    $named = $sets;

    if ( count ( $sets ) > 3 ) {
      if ( $padCheckSyntax )
        padError ( 'the venn chart has ' . count ( $sets ) . ' sets - three at most' );
      $sets = array_slice ( $sets, 0, 3 );
    }

    // The sizes by the members' places: '0' a set alone, '0,1' a pair, '0,1,2' the three.

    $size = [];

    foreach ( $sizes as list ( $members, $v ) ) {

      $key = [];

      foreach ( $members as $member ) {
        $at = array_search ( $member, $sets, TRUE );
        if ( $at === FALSE ) {
          if ( $padCheckSyntax and ! in_array ( $member, $named, TRUE ) )
            padError ( "the venn chart has an overlap with '" . padMakeSafe ( $member, 30 ) . "', a set without a row of its own" );
          continue 2;
        }
        $key [] = $at;
      }

      sort ( $key );
      $size [ implode ( ',', $key ) ] = $v;

    }

    $n = count ( $sets );

    for ( $i = 0; $i < $n; $i++ )
      if ( ( $size [$i] ?? 0 ) <= 0 )
        return '';

    if ( ! $n or ! is_finite ( array_sum ( $size ) ) )
      return '';

    // The count of each region alone, by the bits of its sets: an overlap no larger than
    // its smallest set, a region never below zero.

    $pair = fn ( $a, $b ) => min ( $size ["$a,$b"] ?? 0, $size [$a], $size [$b] );
    $all  = ( $n == 3 ) ? min ( $size ['0,1,2'] ?? 0, $pair ( 0, 1 ), $pair ( 0, 2 ), $pair ( 1, 2 ) ) : 0;
    $only = [];

    if ( $n == 1 )
      $only [1] = $size [0];
    elseif ( $n == 2 )
      $only = [ 1 => $size [0] - $pair ( 0, 1 ), 2 => $size [1] - $pair ( 0, 1 ), 3 => $pair ( 0, 1 ) ];
    else
      $only = [ 1 => $size [0] - $pair ( 0, 1 ) - $pair ( 0, 2 ) + $all,
                2 => $size [1] - $pair ( 0, 1 ) - $pair ( 1, 2 ) + $all,
                4 => $size [2] - $pair ( 0, 2 ) - $pair ( 1, 2 ) + $all,
                3 => $pair ( 0, 1 ) - $all,
                5 => $pair ( 0, 2 ) - $all,
                6 => $pair ( 1, 2 ) - $all,
                7 => $all ];

    $only = array_map ( fn ( $v ) => max ( 0, $v ), $only );

    $title = (string) padTagParm ( 'title', implode ( ', ', $sets ) );

    list ( $id, $svg ) = padChartOpen ( 'venn', [ $sets, $size ], $desc, $title, $width, $height );

    // The circles in units of area: a radius per set, the distance per pair, the centres
    // from those distances.

    $radius = [];
    for ( $i = 0; $i < $n; $i++ )
      $radius [$i] = sqrt ( $size [$i] / M_PI );

    $apart = fn ( $a, $b ) => padChartVennDistance ( $radius [$a], $radius [$b], $pair ( $a, $b ) );

    $at = [ [ 0, 0 ] ];

    if ( $n >= 2 )
      $at [1] = [ $apart ( 0, 1 ), 0 ];

    if ( $n == 3 ) {
      $ab     = $at [1] [0];
      $ac     = $apart ( 0, 2 );
      $bc     = $apart ( 1, 2 );
      $x      = ( $ab > 1e-9 ) ? ( $ac * $ac - $bc * $bc + $ab * $ab ) / ( 2 * $ab ) : $ac;
      $at [2] = [ $x, sqrt ( max ( 0, $ac * $ac - $x * $x ) ) ];
    }

    // Scaled into the chart, room left around it for the names.

    $nameW = 0;
    foreach ( $sets as $set )
      $nameW = max ( $nameW, padChartWidth ( $set ) );

    $x0 = $y0 = INF;
    $x1 = $y1 = -INF;

    foreach ( $at as $i => list ( $x, $y ) ) {
      $x0 = min ( $x0, $x - $radius [$i] );
      $x1 = max ( $x1, $x + $radius [$i] );
      $y0 = min ( $y0, $y - $radius [$i] );
      $y1 = max ( $y1, $y + $radius [$i] );
    }

    $mx    = min ( $width * 0.25, $nameW + 14 );
    $my    = 22;
    $scale = max ( 1e-9, min ( ( $width - 2 * $mx ) / ( $x1 - $x0 ), ( $height - 2 * $my ) / ( $y1 - $y0 ) ) );
    $dx    = $width  / 2 - ( $x0 + $x1 ) / 2 * $scale;
    $dy    = $height / 2 - ( $y0 + $y1 ) / 2 * $scale;
    $c     = [];
    $r     = [];

    foreach ( $at as $i => list ( $x, $y ) ) {
      $c [$i] = [ $dx + $x * $scale, $dy + $y * $scale ];
      $r [$i] = $radius [$i] * $scale;
    }

    foreach ( $c as $i => list ( $x, $y ) )
      $svg .= '<circle class="pc-mark ' . padChartSlot ( $i ) . '" fill-opacity="0.25" cx="' . padChartXY ( $x ) . '" cy="' . padChartXY ( $y ) . '" r="' . padChartXY ( $r [$i] ) . '">'
            . '<title>' . padChartAttr ( $sets [$i] . ': ' . padChartNumber ( $size [$i] ) ) . '</title></circle>';

    foreach ( $c as $i => list ( $x, $y ) )
      $svg .= '<circle class="pc-l' . ( $i + 1 ) . '" cx="' . padChartXY ( $x ) . '" cy="' . padChartXY ( $y ) . '" r="' . padChartXY ( $r [$i] ) . '"/>';

    // The names outside, away from the middle of the centres.

    $mid = [ array_sum ( array_column ( $c, 0 ) ) / $n, array_sum ( array_column ( $c, 1 ) ) / $n ];

    foreach ( $c as $i => list ( $x, $y ) ) {
      $ux     = $x - $mid [0];
      $uy     = $y - $mid [1];
      $len    = sqrt ( $ux * $ux + $uy * $uy );
      list ( $ux, $uy ) = ( $len > 1e-6 ) ? [ $ux / $len, $uy / $len ] : [ 0, -1 ];
      $anchor = ( $ux > 0.35 ) ? 'start' : ( $ux < -0.35 ? 'end' : 'middle' );
      $tx     = $x + $ux * ( $r [$i] + 6 );
      $ty     = $y + $uy * ( $r [$i] + 6 ) + ( $uy > 0.35 ? 11 : ( $uy < -0.35 ? 0 : 4 ) );
      $svg   .= '<text x="' . padChartXY ( $tx ) . '" y="' . padChartXY ( $ty ) . "\" text-anchor=\"$anchor\">" . padChartAttr ( padChartFit ( $sets [$i], $width * 0.4 ) ) . '</text>';
    }

    // The count of every region, at the point of it furthest from any edge on a grid of
    // samples - written when that point leaves room for it.

    $best = [];
    $grid = 60;

    for ( $gx = 0; $gx <= $grid; $gx++ )
      for ( $gy = 0; $gy <= $grid; $gy++ ) {

        $px    = $dx + ( $x0 + ( $x1 - $x0 ) * $gx / $grid ) * $scale;
        $py    = $dy + ( $y0 + ( $y1 - $y0 ) * $gy / $grid ) * $scale;
        $mask  = 0;
        $clear = INF;

        foreach ( $c as $i => list ( $x, $y ) ) {
          $d     = sqrt ( ( $px - $x ) ** 2 + ( $py - $y ) ** 2 );
          $clear = min ( $clear, abs ( $d - $r [$i] ) );
          if ( $d < $r [$i] )
            $mask |= 1 << $i;
        }

        if ( $mask and ( ! isset ( $best [$mask] ) or $clear > $best [$mask] [2] ) )
          $best [$mask] = [ $px, $py, $clear ];

      }

    ksort ( $best );

    foreach ( $best as $mask => list ( $px, $py, $clear ) ) {

      $v = $only [$mask] ?? 0;

      if ( $v <= 0 or $clear < 8 )
        continue;

      $in = [];
      foreach ( $sets as $i => $set )
        if ( $mask & ( 1 << $i ) )
          $in [] = $set;

      $text = padChartNumber ( $v );

      if ( padChartWidth ( $text ) > $clear * 2.4 )
        continue;

      $svg .= '<text x="' . padChartXY ( $px ) . '" y="' . padChartXY ( $py + 4 ) . '" text-anchor="middle">' . $text
            . '<title>' . padChartAttr ( implode ( ' & ', $in ) . ( $n > count ( $in ) ? ' only' : '' ) . ': ' . $text ) . '</title></text>';

    }

    return "$svg</svg>";

  }

  // The distance between two circles whose lens has the area $overlap: halved between
  // touching inside and touching outside, as the lens shrinks the further apart they are.
  // No overlap sets them a little apart; one as large as the smaller circle, inside.

  function padChartVennDistance ( $r1, $r2, $overlap ) {

    $small = min ( $r1, $r2 );

    if ( $overlap <= 0 )
      return $r1 + $r2 + $small * 0.15;

    if ( $overlap >= M_PI * $small * $small - 1e-12 )
      return abs ( $r1 - $r2 );

    $low  = abs ( $r1 - $r2 );
    $high = $r1 + $r2;

    for ( $step = 0; $step < 60; $step++ ) {
      $d = ( $low + $high ) / 2;
      if ( padChartVennLens ( $r1, $r2, $d ) > $overlap )
        $low = $d;
      else
        $high = $d;
    }

    return ( $low + $high ) / 2;

  }

  // The area two circles share at distance $d.

  function padChartVennLens ( $r1, $r2, $d ) {

    if ( $d >= $r1 + $r2 )
      return 0;

    if ( $d <= abs ( $r1 - $r2 ) )
      return M_PI * min ( $r1, $r2 ) ** 2;

    $a = $r1 * $r1 * acos ( max ( -1, min ( 1, ( $d * $d + $r1 * $r1 - $r2 * $r2 ) / ( 2 * $d * $r1 ) ) ) );
    $b = $r2 * $r2 * acos ( max ( -1, min ( 1, ( $d * $d + $r2 * $r2 - $r1 * $r1 ) / ( 2 * $d * $r2 ) ) ) );
    $k = 0.5 * sqrt ( max ( 0, ( -$d + $r1 + $r2 ) * ( $d + $r1 - $r2 ) * ( $d - $r1 + $r2 ) * ( $d + $r1 + $r2 ) ) );

    return $a + $b - $k;

  }

?>
