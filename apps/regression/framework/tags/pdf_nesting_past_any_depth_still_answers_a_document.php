<?php

  // Thirty thousand nested <div> - 300 KB of HTML, from a database or a visitor - still make
  // a document: below some hundreds of levels an element is its text alone, with the text
  // after it, where the walk, a recursion, ended the request on PHP's stack - "Maximum call
  // stack size reached" - since libxml reads any depth.

  $html = str_repeat ( '<div>', 30000 ) . 'deep' . str_repeat ( '</div>', 30000 ) . '<p>after</p>'
        . str_repeat ( '<b>', 30000 ) . 'bold' . str_repeat ( '</b>', 30000 );

  try {
    $pdf   = padPdf ( $html, [ 'compress' => FALSE ] );
    $drawn = 'deep: ' . ( str_contains ( $pdf, '(deep) Tj' ) ? 'written' : 'not written' )
           . ', after: ' . ( str_contains ( $pdf, '(after) Tj' ) ? 'written' : 'not written' )
           . ', bold: ' . ( str_contains ( $pdf, '(bold) Tj' ) ? 'written' : 'not written' )
           . ', pages: ' . substr_count ( $pdf, '/Type /Page ' );
  } catch ( Throwable $e ) {
    $drawn = 'no document: ' . $e->getMessage ();
  }

?>
