<?php

  // Dotted graph paper on A5: a row of dots for every line across - 0.6 mm dots 5 mm apart,
  // 26 of them from 11.5 to 136.5 mm, 39 rows from 10 to 200 - and with major=5 a row of
  // bigger dots, 25 mm apart, over every fifth row, 8 of them. On the screen and on paper a
  // path of the SVG, a dash of no length every 5 mm with round caps; in the PDF the same: 1 J
  // [0 14.17] 0 d, the line drawn half a step past its last dot, to 139 mm, so that rounding
  // does not lose it.

  $html = padUnprotect ( padUnescape ( padCode ( "{printable:graphpaper grid='dots', major=5, paper='a5'/}" ) ) );

  preg_match_all ( '/<path (.*?) d="(M[0-9.]+ [0-9.]+H[0-9.]+)([^"]*)"\/>/', $html, $paths, PREG_SET_ORDER );
  preg_match_all ( '/data-pdf-line="([^"]*)" data-pdf-style="([^"]*)"/', $html, $lines, PREG_SET_ORDER );

  $pdf = padPdf ( $html, [ 'compress' => FALSE ] );

  preg_match ( '/q [0-9. ]+ RG ([0-9.]+) w 1 J \[0 ([0-9.]+)\] 0 d ([0-9.]+) [0-9.]+ m ([0-9.]+) [0-9.]+ l S Q/', $pdf, $first );

  $answer = implode ( ' // ', array_map ( fn ( $p ) => $p [1] . ', ' . ( substr_count ( $p [3], 'M' ) + 1 ) . " rows, the first {$p [2]}", $paths ) )
          . ' // lines ' . count ( $lines ) . ': ' . $lines [0] [1] . ' [' . $lines [0] [2] . '], ' . $lines [1] [1] . ' [' . $lines [1] [2] . ']'
          . ' // pdf: ' . ( $first ? "dots of {$first [1]} pt every {$first [2]} pt from {$first [3]} to {$first [4]} pt" : 'no dots' );

?>
