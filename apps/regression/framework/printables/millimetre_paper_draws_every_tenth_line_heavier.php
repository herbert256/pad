<?php

  // Millimetre paper - spacing=1, major=10 - on A4: 190 squares across and 277 down, 191
  // lines down and 278 across; every tenth from the top left corner heavier, 0.75 pt (0.265
  // mm) against 0.3 pt (0.106 mm): 20 down and 28 across, 48 lines, the other 421 thin, each
  // look a path of the SVG of its own - in the PDF the same widths, 0.75 and 0.3 points.
  // color= the grid.

  $html = padUnprotect ( padUnescape ( padCode ( "{printable:graphpaper spacing=1, major=10, color='#e0915a'/}" ) ) );

  preg_match_all ( '/<path stroke="([^"]*)" stroke-width="([^"]*)" d="([^"]*)"\/>/', $html, $paths, PREG_SET_ORDER );

  $pdf = padPdf ( $html, [ 'compress' => FALSE ] );

  preg_match_all ( '/q ([0-9.]+ [0-9.]+ [0-9.]+) RG ([0-9.]+) w [0-9. ]+ m [0-9. ]+ l S Q/', $pdf, $strokes );

  $answer = implode ( ', ', array_map ( fn ( $p ) => "{$p [1]} {$p [2]} mm: " . substr_count ( $p [3], 'M' ) . ' lines', $paths ) )
          . ' // pdf: ' . implode ( ', ', array_map ( fn ( $w, $n ) => "$n lines of $w pt", array_keys ( array_count_values ( $strokes [2] ) ), array_count_values ( $strokes [2] ) ) )
          . ' in ' . implode ( ', ', array_unique ( $strokes [1] ) );

?>
