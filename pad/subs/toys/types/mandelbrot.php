<?php

  // mandelbrot - the Mandelbrot set, the points c of the plane where z = z² + c stays near
  // home, its edge coloured by how fast the rest escape. Click to zoom in at a point,
  // Shift-click or right-click to zoom out, drag to move; Julia shows the Julia set of the
  // point in the middle, and Play cycles the colours. iterations= how long a point is
  // followed (300 unless given, 50 to 5000); palette= ocean, fire, rainbow or mono; center=
  // the middle of the view, 'x, y'; zoom= how far in (1, up to a trillion); julia= the point
  // c of a Julia set to start with instead, 'x, y'.
  //
  //   {mandelbrot center='-0.7436, 0.1318', zoom=200/}
  //   {mandelbrot julia='-0.8, 0.156', palette='fire'/}

  $padMandelJulia = padToyNumbers ( 'julia', NULL, -2, 2, 2, 2 );

  return padToy ( 'mandelbrot', 'Mandelbrot set', [
    'iterations' => padToyInt ( 'iterations', 300, 50, 5000 ),
    'palette'    => padToyChoice ( 'palette', 'ocean', [ 'ocean', 'fire', 'rainbow', 'mono' ] ),
    'center'     => padToyNumbers ( 'center', $padMandelJulia ? [ 0, 0 ] : [ -0.6, 0 ], -3, 3, 2, 2 ),
    'zoom'       => padToyNumber ( 'zoom', 1, 0.5, 1000000000000 ),
    'julia'      => $padMandelJulia
  ] );

?>
