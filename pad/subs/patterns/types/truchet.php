<?php

  // truchet - Smith's Truchet tiles: squares of the size, each with two quarter circles joining
  // the middles of its sides, turned one way or the other as the seed says - the arcs meet
  // their neighbours' and wander on as endless curves.
  //
  //   {pattern 'truchet', size=20, seed=5, colors='#1d1d1b, #f2c14e'/}

  return [

    'size'    => [ 28, 6, 400 ],
    'colors'  => [ '#f7f4ee', '#3d405b' ],
    'count'   => [ 'tiles', 50000, fn ( $p ) => (int) ceil ( $p ['w'] / $p ['size'] ) * (int) ceil ( $p ['h'] / $p ['size'] ),
                   'a larger size or a smaller width and height' ],

    'draw'    => function ( $p ) {

      $s    = $p ['size'];
      $r    = padPatternNum ( $s / 2 );
      $cols = (int) ceil ( $p ['w'] / $s );
      $rows = (int) ceil ( $p ['h'] / $s );
      $d    = '';

      // One way the arcs curve around the top left and the bottom right corner, the other way
      // around the top right and the bottom left.

      for ( $j = 0; $j < $rows; $j++ )
        for ( $i = 0; $i < $cols; $i++ ) {
          $x = $i * $s;
          $y = $j * $s;
          if ( padPatternRand () < 0.5 )
            $d .= 'M' . padPatternNum ( $x + $s / 2 ) . ',' . padPatternNum ( $y ) . "a$r,$r 0 0 1 -$r,$r"
                . 'M' . padPatternNum ( $x + $s ) . ',' . padPatternNum ( $y + $s / 2 ) . "a$r,$r 0 0 0 -$r,$r";
          else
            $d .= 'M' . padPatternNum ( $x + $s / 2 ) . ',' . padPatternNum ( $y ) . "a$r,$r 0 0 0 $r,$r"
                . 'M' . padPatternNum ( $x ) . ',' . padPatternNum ( $y + $s / 2 ) . "a$r,$r 0 0 1 $r,$r";
        }

      return [ '', "<path d=\"$d\" fill=\"none\" stroke=\"" . padPatternAttr ( $p ['ink'] [0] ) . '" stroke-width="' . padPatternNum ( $s * 0.18 ) . '" stroke-linecap="round"/>' ];

    }

  ];

?>
