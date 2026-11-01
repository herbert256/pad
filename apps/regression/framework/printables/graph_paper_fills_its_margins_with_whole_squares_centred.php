<?php

  // Graph paper of 5 mm squares on A4: inside its margins of 10 mm the room is 190 by 277 mm,
  // 38 squares across and 55 down - 190 by 275 mm - centred, from 10, 11 to 200, 286: 39 lines
  // down and 56 across. They are drawn exactly, by one SVG over the sheet in millimetres -
  // a line drawn as a border is put on a pixel of 96 an inch, squares of 5 mm printed 4.76 and
  // 5.03 mm wide - and stay in the markup for the PDF, hidden on the screen and on paper. A5
  // turned, a margin of 15 by 20 mm and squares of 10 mm: 170 by 118 mm of room, 17 by 11
  // squares, the grid 4 mm further down.

  function graphPaperShow ( $template ) {

    $out = padUnprotect ( padUnescape ( padCode ( $template ) ) );

    preg_match_all ( '/<div class="pp-line pp-pdf" data-pdf-line="([^"]*)"/', $out, $lines );
    preg_match ( '/<svg class="pp-lines" data-pdf-skip aria-hidden="true" focusable="false" viewBox="([^"]*)" fill="none">(.*?)<\/svg>/', $out, $svg );
    preg_match_all ( '/<path stroke="([^"]*)" stroke-width="([^"]*)" d="([^"]*)"\/>/', $svg [2] ?? '', $paths, PREG_SET_ORDER );

    $down   = array_values ( array_filter ( $lines [1], fn ( $l ) => explode ( ' ', $l ) [0] == explode ( ' ', $l ) [2] ) );
    $across = array_values ( array_diff ( $lines [1], $down ) );

    return printablesRead ( $out, 0 ) . ' // ' . count ( $down ) . ' down, ' . reset ( $down ) . ' to ' . end ( $down )
         . ' // ' . count ( $across ) . ' across, ' . reset ( $across ) . ' to ' . end ( $across )
         . ' // svg ' . ( $svg [1] ?? 'none' ) . ': '
         . implode ( ', ', array_map ( fn ( $p ) => "{$p [1]} {$p [2]} mm, " . substr_count ( $p [3], 'M' ) . ' lines', $paths ) )
         . ( preg_match ( '/\.pp-pdf\{display:none\}/', $out ) ? ' // the lines of the markup hidden' : '' );

  }

  $answer = graphPaperShow ( '{printable:graphpaper/}' ) . ' ## '
          . graphPaperShow ( "{printable:graphpaper paper='a5', landscape, margin='15 20', spacing=10/}" );

?>
