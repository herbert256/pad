<?php

  // mesh - a soft mesh gradient: a glow of every ink, the size its radius, at a place the seed
  // picks, each fading out over the background and the glows before it.
  //
  //   {pattern 'mesh', seed=21, colors='#fdfcfb, #a18cd1, #fbc2eb, #8fd3f4'/}
  //
  // Each glow is a radial gradient from its ink to its ink made transparent; at least three
  // are drawn, the inks taken again from the first when there are fewer.

  return [

    'size'    => [ 260, 20, 4000 ],
    'colors'  => [ '#fdfcfb', '#ff9a8b', '#ff6a88', '#ffd3a5', '#a18cd1', '#fbc2eb' ],

    'draw'    => function ( $p ) {

      $ink   = $p ['ink'];
      $count = min ( 8, max ( 3, count ( $ink ) ) );
      $defs  = '';
      $body  = '';

      for ( $k = 0; $k < $count; $k++ ) {
        $color = padPatternAttr ( $ink [ $k % count ( $ink ) ] );
        $id    = $p ['id'] . '-' . $k;
        $defs .= "<radialGradient id=\"$id\" gradientUnits=\"userSpaceOnUse\" cx=\"" . padPatternNum ( padPatternRand () * $p ['w'], 1 )
               . '" cy="' . padPatternNum ( padPatternRand () * $p ['h'], 1 ) . '" r="' . padPatternNum ( $p ['size'] * ( 0.7 + 0.6 * padPatternRand () ), 1 ) . '">'
               . "<stop offset=\"0\" stop-color=\"$color\"/><stop offset=\"1\" stop-color=\"$color\" stop-opacity=\"0\"/></radialGradient>";
        $body .= padPatternFill ( $p, $id );
      }

      return [ $defs, $body ];

    }

  ];

?>
