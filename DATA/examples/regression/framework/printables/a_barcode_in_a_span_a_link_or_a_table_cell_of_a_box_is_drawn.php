<?php

  // An SVG - a barcode, a QR code - in a box of a printable is drawn in the PDF wherever the
  // screen shows it: straight in the box, in a block, and in an inline element or a table
  // cell too - a label laid out as a table, the code in one cell and the name in the next.
  // In a span, a link or a cell it was left out: the inline content and the cells of a
  // table are set as text, and the SVG was skipped there. Its rectangle is 15 by 7.5 points,
  // the 20 by 10 pixels of the SVG; in a span the text around it stays, in a cell the next
  // cell's text stands beside it.

  $svg = '<svg width="20" height="10" viewbox="0 0 20 10"><rect width="20" height="10"/></svg>';

  $answer = [];

  foreach ( [ 'div'   => "<div>$svg</div>",
              'p'     => "<p>Code $svg</p>",
              'li'    => "<ul><li>$svg</li></ul>",
              'span'  => "<p>Before <span>$svg</span> after</p>",
              'a'     => "<a href=\"https://example.org/\">$svg</a>",
              'td'    => "<table><tr><td>$svg</td><td>Ann</td></tr></table>" ] as $what => $html ) {

    $pdf = padPdf ( '<section data-pdf-sheet="100 50"><div data-pdf-box="5 5 80 40" data-pdf-style="pad=1;size=9">' . $html . '</div></section>',
                    [ 'compress' => FALSE ] );

    preg_match_all ( '/\(([^)]*)\) Tj/', $pdf, $words );

    $answer [] = "$what: " . substr_count ( $pdf, ' 15 7.5 re f' ) . ' drawn' . ( $words [1] ? ', ' . preg_replace ( '/[^ -~]/', '?', implode ( ' ', $words [1] ) ) : '' );

  }

  $answer = implode ( ' // ', $answer );

?>
