<?php

  // Parts of a whole: {chart 'pie', data='budget', label='post', value='amount'} - and
  // 'donut', the same as a ring with the total in its middle.
  //
  // Only positive values are parts of a whole, so a zero or a negative one is left out.
  // The slices go clockwise from twelve o'clock in the order of the rows, parted by a 2px
  // line of the surface; past eight slices the smallest ones are folded into one 'Other',
  // since a ninth colour would be made up. A legend beside the circle names every slice
  // with its value and its share.

  function padChartPie ( $kind, $points, $title, $width, $height ) {

    $points = array_values ( array_filter ( $points, fn ( $point ) => $point [1] > 0 ) );
    $folded = ( count ( $points ) > 8 );

    if ( $folded ) {

      $values = array_column ( $points, 1 );
      arsort ( $values );
      $keep   = array_slice ( array_keys ( $values ), 0, 7 );
      $kept   = [];
      $other  = 0;

      foreach ( $points as $i => $point )
        if ( in_array ( $i, $keep ) )
          $kept [] = $point;
        else
          $other += $point [1];

      $points   = $kept;
      $points[] = [ 'Other', $other ];

    }

    $total = array_sum ( array_column ( $points, 1 ) );

    if ( ! $points or ! is_finite ( $total ) or $total <= 0 )
      return '';

    $share = fn ( $v ) => padChartNumber ( round ( $v / $total * 100, 1 ) ) . '%';
    $desc  = [];

    foreach ( $points as $point )
      $desc [] = $point [0] . ': ' . padChartNumber ( $point [1] ) . ' (' . $share ( $point [1] ) . ')';

    list ( $id, $svg ) = padChartOpen ( $kind, $points, $desc, $title, $width, $height );

    $r     = max ( 10, min ( $height - 20, $width * 0.5 - 20 ) / 2 );
    $cx    = 10 + $r;
    $cy    = $height / 2;
    $inner = ( $kind == 'donut' ) ? $r * 0.6 : 0;
    $a     = 0;
    $slot  = fn ( $i ) => ( $folded and $i == count ( $points ) - 1 ) ? 'pc-co' : padChartSlot ( $i );

    foreach ( $points as $i => $point ) {

      $sweep = $point [1] / $total * 2 * M_PI;
      $d     = padChartArc ( $cx, $cy, $inner, $r, $a, $a + $sweep );
      $a    += $sweep;

      $svg .= '<path class="pc-mark pc-gap ' . $slot ( $i ) . "\" fill-rule=\"evenodd\" d=\"$d\"><title>"
            . padChartAttr ( $point [0] . ': ' . padChartNumber ( $point [1] ) . ' (' . $share ( $point [1] ) . ')' ) . '</title></path>';

    }

    if ( $inner )
      $svg .= '<text class="pc-big" x="' . padChartXY ( $cx ) . '" y="' . padChartXY ( $cy + 6 ) . '" text-anchor="middle">' . padChartNumber ( $total ) . '</text>';

    // The legend, a row per slice, centred beside the circle.

    $x = $cx + $r + 24;
    $y = max ( 4, $cy - count ( $points ) * 9 );

    foreach ( $points as $i => $point ) {
      $text  = $point [0] . '  ' . padChartNumber ( $point [1] ) . ' (' . $share ( $point [1] ) . ')';
      $svg  .= '<rect class="' . $slot ( $i ) . '" x="' . padChartXY ( $x ) . '" y="' . padChartXY ( $y + $i * 18 ) . '" width="10" height="10" rx="2"/>'
             . '<text x="' . padChartXY ( $x + 16 ) . '" y="' . padChartXY ( $y + $i * 18 + 9 ) . '">' . padChartAttr ( padChartFit ( $text, $width - $x - 20 ) ) . '</text>';
    }

    return "$svg</svg>";

  }

?>
