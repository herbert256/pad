<?php

  // A table row taller than a page is set over the pages, its lines continued below the
  // header row on the next one, where it was set whole and its text ran down past the
  // bottom of the page - to y = -1843 - and was lost.

  $html = '<table><tr><th>Item</th><th>Notes</th></tr><tr><td>x</td><td>'
        . str_repeat ( 'line of text that wraps in a cell ', 600 ) . '</td></tr></table><p>after</p>';

  $pdf = padPdf ( $html, [ 'compress' => FALSE ] );

  preg_match_all ( '/([-0-9.]+) ([-0-9.]+) Td/', $pdf, $found );

  $lowest = min ( array_map ( 'floatval', $found [2] ) );
  $placed = $lowest >= 0 ? 'every line on a page' : 'lines below the page';
  $pages  = substr_count ( $pdf, '/Type /Page ' ) + substr_count ( $pdf, "/Type /Page\n" ) + substr_count ( $pdf, '/Type /Page>' );

?>
