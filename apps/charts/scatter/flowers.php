<?php

  // Petal length and width, in centimetres, of three kinds of iris.

  $flowers = [];

  $measured = [
    'setosa'     => [ [1.4,0.2], [1.3,0.2], [1.5,0.2], [1.7,0.4], [1.4,0.3], [1.6,0.2], [1.5,0.4], [1.2,0.2] ],
    'versicolor' => [ [4.7,1.4], [4.5,1.5], [4.9,1.5], [4.0,1.3], [4.6,1.5], [3.9,1.1], [4.4,1.4], [4.1,1.0] ],
    'virginica'  => [ [6.0,2.5], [5.1,1.9], [5.9,2.1], [5.6,1.8], [5.8,2.2], [6.6,2.1], [5.5,1.8], [6.1,2.3] ]
  ];

  foreach ( $measured as $kind => $petals )
    foreach ( $petals as list ( $length, $width ) )
      $flowers [] = [ 'kind' => $kind, 'length' => $length, 'width' => $width ];

?>
