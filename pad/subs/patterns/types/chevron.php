<?php

  // chevron - bold zigzag bands the size apart, the inks taking turns row by row, their
  // corners sharp. Drawn as it is: the seed changes nothing.
  //
  //   {pattern:chevron size=30, colors='#fbf7f0, #3d405b, #e07a5f'/}

  return [

    'size'    => [ 24, 4, 300 ],
    'colors'  => [ '#fbf7f0', '#e07a5f', '#f2cc8f' ],
    'count'   => [ 'zigzags', 50000, fn ( $p ) => ( (int) ceil ( $p ['h'] / $p ['size'] ) + 2 ) * (int) ceil ( ( $p ['w'] + 2 * $p ['size'] ) / $p ['size'] ),
                   'a larger size or a smaller width and height' ],

    'draw'    => function ( $p ) {

      $s     = $p ['size'];
      $n     = count ( $p ['ink'] );
      $paths = array_fill ( 0, $n, '' );
      $rows  = (int) ceil ( $p ['h'] / $s ) + 1;
      $count = (int) ceil ( ( $p ['w'] + 2 * $s ) / $s );
      $zig   = 'l' . implode ( ' ', array_fill ( 0, $count, padPatternNum ( $s / 2 ) . ',-' . padPatternNum ( $s / 2 ) . ' ' . padPatternNum ( $s / 2 ) . ',' . padPatternNum ( $s / 2 ) ) );

      for ( $j = 0; $j <= $rows; $j++ )
        $paths [ $j % $n ] .= 'M-' . padPatternNum ( $s ) . ',' . padPatternNum ( $j * $s ) . $zig;

      $body = '';

      foreach ( $paths as $i => $d )
        if ( $d !== '' )
          $body .= "<path d=\"$d\" fill=\"none\" stroke=\"" . padPatternAttr ( $p ['ink'] [$i] ) . '" stroke-width="' . padPatternNum ( $s * 0.35 ) . '" stroke-linejoin="miter" stroke-miterlimit="4"/>';

      return [ '', $body ];

    }

  ];

?>
