<?php

  // stripes - diagonal stripes, a band of each ink half the size wide and the background
  // between them, turned by angle= (45 degrees; 0 stands them upright, 90 lays them down).
  // A tile repeated by a <pattern>: the seed changes nothing.
  //
  //   {pattern 'stripes', size=12, angle=-30, colors='#fff, #e9c46a, #2a9d8f'/}

  return [

    'size'    => [ 16, 2, 400 ],
    'colors'  => [ '#f3efe6', '#e2d6c2' ],
    'options' => [ 'angle' => [ 45, -360, 360 ] ],

    'draw'    => function ( $p ) {

      $s    = $p ['size'];
      $tile = padPatternNum ( $s * count ( $p ['ink'] ) );
      $rect = '';

      foreach ( $p ['ink'] as $i => $ink )
        $rect .= '<rect x="' . padPatternNum ( $i * $s ) . '" width="' . padPatternNum ( $s / 2 ) . "\" height=\"$tile\" fill=\"" . padPatternAttr ( $ink ) . '"/>';

      return [ "<pattern id=\"{$p ['id']}\" width=\"$tile\" height=\"$tile\" patternUnits=\"userSpaceOnUse\" patternTransform=\"rotate("
               . padPatternNum ( $p ['angle'] ) . ")\">$rect</pattern>",
               padPatternFill ( $p, $p ['id'] ) ];

    }

  ];

?>
