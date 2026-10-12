<?php

  // Thirty quotes in each other in a {pdf}: the indent grows no further than half the
  // text width, so the text stays on the page, where it was set off its right edge - a
  // character a line, over four pages.

  $html = str_repeat ( '<blockquote>', 30 ) . '<p>' . str_repeat ( 'word ', 40 ) . '</p>' . str_repeat ( '</blockquote>', 30 );
  $pdf  = padPdf ( $html, [ 'compress' => FALSE ] );

  preg_match_all ( '#([\d.]+) [\d.]+ Td#', $pdf, $found );

  $where = ( max ( $found [1] ) < 595 - 56 ) ? 'on the page' : 'off the page at x ' . max ( $found [1] );
  $pages = substr_count ( $pdf, '/Type /Page ' );

?>
