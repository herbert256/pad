<?php

  // The cells of the voronoi pattern (types/voronoi.php).
  //
  // padPatternClip  the part of a polygon on one side of a line

  // The part of a polygon on the near side of the line through (mx, my) square to (dx, dy):
  // where (x - m) . d is not above 0 - Sutherland and Hodgman's clipping, one edge.

  function padPatternClip ( $polygon, $mx, $my, $dx, $dy ) {

    $out   = [];
    $count = count ( $polygon );

    for ( $k = 0; $k < $count; $k++ ) {
      list ( $x1, $y1 ) = $polygon [$k];
      list ( $x2, $y2 ) = $polygon [ ( $k + 1 ) % $count ];
      $f1 = ( $x1 - $mx ) * $dx + ( $y1 - $my ) * $dy;
      $f2 = ( $x2 - $mx ) * $dx + ( $y2 - $my ) * $dy;
      if ( $f1 <= 0 )
        $out [] = [ $x1, $y1 ];
      if ( ( $f1 <= 0 ) != ( $f2 <= 0 ) ) {
        $t     = $f1 / ( $f1 - $f2 );
        $out [] = [ $x1 + $t * ( $x2 - $x1 ), $y1 + $t * ( $y2 - $y1 ) ];
      }
    }

    return $out;

  }

?>
