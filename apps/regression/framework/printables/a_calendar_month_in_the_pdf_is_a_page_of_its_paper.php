<?php

  // In a PDF a month of the calendar is a page of its paper - A4, 595.28 by 841.89 points, or
  // A5 turned - its texts set where the screen has them, in their colours: the month and the
  // red days in #c0392b (0.75 0.22 0.17), the weekdays grey, the other days black; and the
  // lines of the grid, drawn exactly by an SVG on the screen, are drawn by the PDF from the
  // markup, 8 down and 6 across, 0.5 pt.

  $html = padUnprotect ( padUnescape ( padCode ( "{printable:calendar month='2027-04', holidays='nl'/}{printable:calendar month='2027-02', paper='a5', landscape/}" ) ) );
  $pdf  = padPdf ( $html, [ 'compress' => FALSE ] );

  preg_match_all ( '#/MediaBox \[0 0 ([0-9.]+) ([0-9.]+)\]#', $pdf, $boxes, PREG_SET_ORDER );
  preg_match_all ( '#stream\n(.*?)\nendstream#s', $pdf, $streams );

  $first = $streams [1] [0] ?? '';

  preg_match_all ( '/BT ([0-9.]+ [0-9.]+ [0-9.]+) rg \/F\d+ ([0-9.]+) Tf [0-9.]+ [0-9.]+ Td \(((?:[^()\\\\]|\\\\.)*)\) Tj ET/', $first, $texts, PREG_SET_ORDER );

  $red = array_map ( fn ( $t ) => $t [3], array_filter ( $texts, fn ( $t ) => $t [1] == '0.75 0.22 0.17' ) );

  $answer = count ( $boxes ) . ' pages: ' . implode ( ', ', array_map ( fn ( $b ) => "{$b [1]} by {$b [2]}", $boxes ) )
          . ' // ' . count ( $texts ) . ' texts, red: ' . implode ( ' ', $red )
          . ' // ' . preg_match_all ( '/q [0-9. ]+ RG 0\.5 w [0-9. ]+ m [0-9. ]+ l S Q/', $first ) . ' lines of 0.5 pt';

?>
