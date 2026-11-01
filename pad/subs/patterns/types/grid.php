<?php

  // grid - graph paper: a thin line every size pixels in the first ink and a bolder one every
  // fifth in the second. A tile repeated by a <pattern>: the seed changes nothing.
  //
  //   {pattern 'grid', size=12, colors='#fffdf8, #f2d7d5, #d98880'/}

  return [

    'size'    => [ 16, 3, 200 ],
    'colors'  => [ '#fbfbf9', '#dde6ee', '#b4c7d9' ],

    'draw'    => function ( $p ) {

      $s    = $p ['size'];
      $t    = padPatternNum ( $s * 5 );
      $thin = '';

      for ( $k = 1; $k <= 4; $k++ ) {
        $at    = padPatternNum ( $s * $k );
        $thin .= "M0,{$at}H{$t}M$at,0V$t";
      }

      $width = max ( 0.5, min ( 2, $s / 20 ) );

      return [ "<pattern id=\"{$p ['id']}\" width=\"$t\" height=\"$t\" patternUnits=\"userSpaceOnUse\" fill=\"none\">"
               . "<path d=\"$thin\" stroke=\"" . padPatternAttr ( $p ['ink'] [0] ) . '" stroke-width="' . padPatternNum ( $width ) . '"/>'
               . "<path d=\"M0,0H{$t}M0,{$t}H{$t}M0,0V{$t}M$t,0V$t\" stroke=\"" . padPatternAttr ( $p ['ink'] [1] ?? $p ['ink'] [0] )
               . '" stroke-width="' . padPatternNum ( $width * 2 ) . '"/>'
               . '</pattern>',
               padPatternFill ( $p, $p ['id'] ) ];

    }

  ];

?>
