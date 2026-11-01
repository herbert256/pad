<?php

  // checks - a checkerboard of squares of the size, the dark squares in the inks, the light
  // ones the background. A tile repeated by a <pattern>: the seed changes nothing.
  //
  //   {pattern 'checks', size=10, colors='#ffffff, #222222'/}

  return [

    'size'    => [ 20, 2, 400 ],
    'colors'  => [ '#f4f1ea', '#e3dccd' ],

    'draw'    => function ( $p ) {

      $s = padPatternNum ( $p ['size'] );
      $t = padPatternNum ( $p ['size'] * 2 );

      return [ "<pattern id=\"{$p ['id']}\" width=\"$t\" height=\"$t\" patternUnits=\"userSpaceOnUse\">"
               . "<rect width=\"$s\" height=\"$s\" fill=\"" . padPatternAttr ( $p ['ink'] [0] ) . '"/>'
               . "<rect x=\"$s\" y=\"$s\" width=\"$s\" height=\"$s\" fill=\"" . padPatternAttr ( $p ['ink'] [1] ?? $p ['ink'] [0] ) . '"/>'
               . '</pattern>',
               padPatternFill ( $p, $p ['id'] ) ];

    }

  ];

?>
