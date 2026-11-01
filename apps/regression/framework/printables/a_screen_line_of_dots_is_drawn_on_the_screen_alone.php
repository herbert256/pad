<?php

  // screen is what the screen alone shows - left out on paper and in the PDF - for a line of
  // dots too: it is drawn in an SVG of its own, pp-screen (hidden in print) and data-pdf-skip,
  // and the line itself leaves nothing for the PDF to draw. A line of dots for paper stays in
  // the sheet's SVG and in a hidden line the PDF draws; a dashed screen line stays a border
  // of screen; with no line of screen there is no second SVG.

  include_once PAD . 'subs/printables/_lib/printable.php';

  $sheet = function ( $prims ) {
    $css   = [];
    $exact = padPrintableExact ( [ 'paper' => [ 210, 297 ] ], $prims );
    $out   = [ 'svg' => preg_replace ( '/ viewBox="[^"]*"| fill="none"| aria-hidden="true"| focusable="false"/', '', padUnprotect ( $exact [0] ) ), 'at' => $exact [1] ];
    foreach ( $prims as $prim )
      $out ['lines'] [] = padUnprotect ( padPrintablePrim ( $css, $prim ) );
    return $out;
  };

  $paper  = [ 'line', 10, 20, 100, 20, [ 'stroke' => '#336699', 'width' => 1, 'dots' => 5 ] ];
  $screen = [ 'line', 10, 30, 100, 30, [ 'stroke' => '#ff0000', 'width' => 1, 'dots' => 5, 'screen' => TRUE ] ];
  $dashed = [ 'line', 10, 40, 100, 40, [ 'stroke' => '#ff0000', 'width' => 1, 'dash' => TRUE, 'screen' => TRUE ] ];

  $json = json_encode ( [ 'both' => $sheet ( [ $paper, $screen, $dashed ] ), 'screen alone' => $sheet ( [ $screen ] ), 'paper alone' => $sheet ( [ $paper ] ) ],
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
