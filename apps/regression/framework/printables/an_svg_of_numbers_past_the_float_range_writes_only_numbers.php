<?php

  // An SVG in a box of a printable whose numbers are past the range of a float - 1e400 is
  // INF - writes only numbers into the PDF: such a width, height or viewBox is none, and the
  // SVG takes the size it has without it; a text size or a stroke past 1000 points is held to
  // 1000; a shape at a place past the range is left out. A width of 1e400 made the scale INF
  // times 0, NAN, and the content stream held "nan 104.3 nan 0 re f" and "/F1 1 Tf nan 104.3
  // Td", which no reader takes.

  $box = fn ( $svg ) => '<section data-pdf-sheet="100 50"><div data-pdf-box="5 5 60 30" data-pdf-style="pad=1;size=9">'
                      . "<p>before</p>$svg<p>after</p></div></section>";

  $answer = [];

  foreach ( [ 'a width of 1e400'   => '<svg width="1e400" height="20" viewbox="0 0 10 10"><rect width="5" height="5"/><text x="1" y="5">T</text></svg>',
              'a viewBox of 1e400' => '<svg width="20" height="20" viewbox="0 0 1e400 10"><rect width="5" height="5"/></svg>',
              'a size of 1e400'    => '<svg width="20" height="20" viewbox="0 0 10 10"><text x="1" y="5" font-size="1e400">T</text><rect width="5" height="5" stroke="#000" stroke-width="1e400"/></svg>',
              'shapes at 1e400'    => '<svg width="20" height="20" viewbox="0 0 10 10"><rect x="1e400" width="5" height="5"/><rect width="1e400" height="5"/><text x="1e400" y="5">T</text><path d="M0 0 L1e400 5 Z"/><rect width="2" height="2"/></svg>' ] as $what => $svg ) {

    $pdf  = padPdf ( $box ( $svg ), [ 'compress' => FALSE ] );
    $ops  = preg_match_all ( '/^(?:q [^\n]* re f Q|BT [^\n]* Tj ET|q [^\n]* [fSB] Q)$/m', $pdf, $all ) ? implode ( ' | ', preg_replace ( '/^BT [^\n]*\/F\d+ ([-0-9.]+) Tf .*$/', 'text $1', $all [0] ) ) : '';

    $answer [] = "$what: " . ( preg_match ( '/\b(?:nan|inf|-inf)\b/i', $pdf ) ? 'nan or inf in the PDF' : 'numbers only' ) . ', ' . ( $ops !== '' ? $ops : 'nothing drawn' );

  }

  $answer = implode ( ' // ', $answer );

?>
