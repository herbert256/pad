<?php

  // The SVG of a barcode or a QR code in a box of a printable is drawn in the PDF: its white
  // ground and its bars as filled rectangles - the bars a path of M, h, v and z, turned into
  // the PDF's own path - its digits as text, in Courier for the monospaced font of the SVG.
  // Outside a printable an SVG is left out of a PDF as before.

  $label = padUnprotect ( padUnescape ( padCode ( "{printable:labels copies=1}{barcode:ean13 '400638133393'/}{/printable:labels}" ) ) );
  $plain = padUnprotect ( padUnescape ( padCode ( "<p>Plain</p>{barcode:ean13 '400638133393'/}" ) ) );

  $count = function ( $html ) {
    $pdf = padPdf ( $html, [ 'compress' => FALSE ] );
    preg_match_all ( '/\(([0-9]+)\) Tj/', $pdf, $digits );
    return substr_count ( $pdf, ' re f Q' ) . ' rectangles, ' . substr_count ( $pdf, ' f Q' ) - substr_count ( $pdf, ' re f Q' ) . ' paths, digits '
         . implode ( ' ', $digits [1] ) . ( str_contains ( $pdf, '/BaseFont /Courier' ) ? ' in Courier' : '' );
  };

  $answer = 'in a label: ' . $count ( $label ) . ' // in a page: ' . $count ( $plain );

?>
