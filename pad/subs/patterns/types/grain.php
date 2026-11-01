<?php

  // grain - film grain: fractal noise of the browser's feTurbulence, the size its grain in
  // pixels, in the ink over the background - laid over a colour or a picture with opacity=.
  //
  //   {pattern 'grain', size=1.2, opacity=0.4, colors='none, #000'/}
  //
  // The noise is drawn by the browser from the filter's numbers, the seed among them, so the
  // page carries a few hundred bytes for any size of picture: its red channel, made steeper,
  // is the alpha of a flood of the ink.

  return [

    'size'    => [ 1.6, 0.5, 20 ],
    'colors'  => [ '#f1ede4', '#3b3a36' ],
    'opacity' => 0.5,

    'draw'    => function ( $p ) {

      $id   = $p ['id'];
      $seed = padFakeNext () % 10000;

      return [ "<filter id=\"$id\" x=\"0\" y=\"0\" width=\"100%\" height=\"100%\" color-interpolation-filters=\"sRGB\">"
               . '<feTurbulence type="fractalNoise" baseFrequency="' . padPatternNum ( 1 / $p ['size'], 3 ) . "\" numOctaves=\"3\" seed=\"$seed\" stitchTiles=\"stitch\" result=\"noise\"/>"
               . '<feColorMatrix in="noise" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 3 0 0 0 -1" result="grain"/>'
               . '<feFlood flood-color="' . padPatternAttr ( $p ['ink'] [0] ) . '"/>'
               . '<feComposite in2="grain" operator="in"/>'
               . '</filter>',
               '<rect width="' . padPatternNum ( $p ['w'] ) . '" height="' . padPatternNum ( $p ['h'] ) . "\" filter=\"url(#$id)\"/>" ];

    }

  ];

?>
