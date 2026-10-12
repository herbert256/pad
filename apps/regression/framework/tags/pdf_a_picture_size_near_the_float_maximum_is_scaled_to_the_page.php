<?php

  // A picture's size near the float maximum in a {pdf} is scaled to the column like any
  // oversized one: height="1e308" on a picture wider than tall made its width INF, the
  // scale 0 and the matrix nan, and the text after it was lost.

  $pdf = padPdf ( '<img src="assets/photo.jpg" height="1e308"><p>After</p>', [ 'compress' => FALSE ] );

  preg_match_all ( '#q (\S+) 0 0 (\S+) \S+ \S+ cm#', $pdf, $found );

  $sizes = implode ( ' ', array_merge ( $found [1], $found [2] ) );
  $after = str_contains ( $pdf, '(After) Tj' ) ? 'After kept' : 'After lost';

?>
