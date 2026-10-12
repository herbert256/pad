<?php

  // Text from a database or a file in a PDF: a NUL byte ended libxml's reading there, and
  // past 256 nested elements it dropped the rest - "After" was missing from both documents.

  $pdfText = function ( $pdf ) {
    preg_match_all ( '/stream\r?\n(.*?)\r?\nendstream/s', $pdf, $m );
    return implode ( '', array_map ( fn ( $one ) => (string) @gzuncompress ( $one ), $m [1] ) );
  };

  $pdfNul  = str_contains ( $pdfText ( padPdf ( "<h1>T</h1><p>\x00 x</p><p>After</p>", [] ) ), '(After)' ) ? 'read' : 'lost';
  $pdfDeep = str_contains ( $pdfText ( padPdf ( str_repeat ( '<b>', 260 ) . 'deep' . str_repeat ( '</b>', 260 ) . '<p>After</p>', [] ) ), '(After)' ) ? 'read' : 'lost';

?>
