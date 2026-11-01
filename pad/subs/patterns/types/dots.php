<?php

  // dots - polka dots in staggered rows, two to a tile of the size, the second in the second
  // ink when there is one. A tile repeated by a <pattern>: the seed changes nothing.
  //
  //   {pattern 'dots', size=24, colors='#fdfbf7, #e0c9a6'/}

  return [

    'size'    => [ 20, 3, 400 ],
    'colors'  => [ '#f6f3ee', '#d9c9b0' ],

    'draw'    => function ( $p ) {

      $s   = $p ['size'];
      $r   = padPatternNum ( $s * 0.15 );
      $one = padPatternAttr ( $p ['ink'] [0] );
      $two = padPatternAttr ( $p ['ink'] [1] ?? $p ['ink'] [0] );

      return [ "<pattern id=\"{$p ['id']}\" width=\"" . padPatternNum ( $s ) . '" height="' . padPatternNum ( $s ) . '" patternUnits="userSpaceOnUse">'
               . '<circle cx="' . padPatternNum ( $s / 4 ) . '" cy="' . padPatternNum ( $s / 4 ) . "\" r=\"$r\" fill=\"$one\"/>"
               . '<circle cx="' . padPatternNum ( $s * 3 / 4 ) . '" cy="' . padPatternNum ( $s * 3 / 4 ) . "\" r=\"$r\" fill=\"$two\"/>"
               . '</pattern>',
               padPatternFill ( $p, $p ['id'] ) ];

    }

  ];

?>
