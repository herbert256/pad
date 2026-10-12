<?php

  // A picture's width in a {pdf}: 100% is the column, where it was 100 pixels; a negative
  // width is none, where it flipped the scale and drew the picture larger than the page;
  // 1e999 is none, where it wrote nan and lost the lines after it.

  $pdf = padPdf ( '<img src="assets/photo.jpg" width="100%"><img src="assets/photo.jpg" width="-100"><img src="assets/photo.jpg" width="1e999">',
                  [ 'compress' => FALSE ] );

  preg_match_all ( '#q (\S+) 0 0 (\S+) \S+ \S+ cm#', $pdf, $found );

  $widths = implode ( ' ', $found [1] );

?>
