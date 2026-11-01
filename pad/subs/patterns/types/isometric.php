<?php

  // isometric - tumbling blocks: cubes seen from above in isometric view, the size their edge,
  // the top in the first ink, the left side in the second, the right in the third. A tile
  // repeated by a <pattern>: the seed changes nothing.
  //
  //   {pattern 'isometric', size=18, colors='#000, #f4e9d8, #d4a373, #6b4f3a'/}
  //
  // The cubes are hexagons standing on a point, their centres a row apart of 1.5 size and
  // offset by half a cube every other row; the tile, the width of a cube and two rows high,
  // holds the five cubes that reach into it.

  return [

    'size'    => [ 22, 4, 300 ],
    'colors'  => [ '#e8edf2', '#f7f9fb', '#c5d3e0', '#8fa6bd' ],

    'draw'    => function ( $p ) {

      $s     = $p ['size'];
      $w     = sqrt ( 3 ) * $s;
      $half  = padPatternNum ( $w / 2 );
      $edge  = padPatternNum ( $s / 2 );
      $side  = padPatternNum ( $s );
      $faces = [ "l-$half,-{$edge}l$half,-{$edge}l$half,{$edge}z",
                 "l-$half,-{$edge}v{$side}l$half,{$edge}z",
                 "v{$side}l$half,-{$edge}v-{$side}z" ];
      $paths = [ '', '', '' ];

      foreach ( [ [ 0, 0 ], [ $w, 0 ], [ $w / 2, 1.5 * $s ], [ 0, 3 * $s ], [ $w, 3 * $s ] ] as list ( $cx, $cy ) )
        foreach ( $faces as $f => $face )
          $paths [$f] .= 'M' . padPatternNum ( $cx ) . ',' . padPatternNum ( $cy ) . $face;

      $ink  = $p ['ink'];
      $tile = '';

      foreach ( $paths as $f => $d ) {
        $color = padPatternAttr ( $ink [ $f % count ( $ink ) ] );
        $tile .= "<path d=\"$d\" fill=\"$color\" stroke=\"$color\" stroke-width=\"0.5\" stroke-linejoin=\"round\"/>";
      }

      return [ "<pattern id=\"{$p ['id']}\" width=\"" . padPatternNum ( $w ) . '" height="' . padPatternNum ( 3 * $s ) . "\" patternUnits=\"userSpaceOnUse\">$tile</pattern>",
               padPatternFill ( $p, $p ['id'] ) ];

    }

  ];

?>
