<?php

  // waves - rows of wavy lines the size apart, the inks taking turns, each row shifted along
  // the one above by a step the seed chooses, so the crests run in a slant of their own.
  //
  //   {pattern 'waves', size=14, seed=4, colors='#f2f7fb, #9cc3dd, #5b9bc4, #2e6f9e'/}

  return [

    'size'    => [ 18, 4, 200 ],
    'colors'  => [ '#f2f7fb', '#9cc3dd', '#5b9bc4' ],

    'draw'    => function ( $p ) {

      $s     = $p ['size'];
      $half  = 2 * $s;
      $crest = 0.7 * $s;
      $shift = padPatternRand () * 2 * $half;
      $n     = count ( $p ['ink'] );
      $paths = array_fill ( 0, $n, '' );
      $rows  = (int) ceil ( $p ['h'] / $s );
      $wave  = 'q' . padPatternNum ( $half / 2 ) . ',-' . padPatternNum ( $crest ) . ' ' . padPatternNum ( $half ) . ',0t';
      $more  = padPatternNum ( $half ) . ',0';

      for ( $j = -1; $j <= $rows; $j++ ) {
        $x     = - $half - fmod ( ( $j + 1 ) * $shift, 2 * $half );
        $count = (int) ceil ( ( $p ['w'] - $x ) / $half );
        $paths [ ( $j + 1 ) % $n ] .= 'M' . padPatternNum ( $x ) . ',' . padPatternNum ( $j * $s + $crest / 4 ) . $wave
                                    . implode ( ' ', array_fill ( 0, $count, $more ) );
      }

      $body = '';

      foreach ( $paths as $i => $d )
        if ( $d !== '' )
          $body .= "<path d=\"$d\" fill=\"none\" stroke=\"" . padPatternAttr ( $p ['ink'] [$i] ) . '" stroke-width="' . padPatternNum ( $s * 0.2 ) . '" stroke-linecap="round"/>';

      return [ '', $body ];

    }

  ];

?>
