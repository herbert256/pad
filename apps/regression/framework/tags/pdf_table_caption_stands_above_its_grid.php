<?php

  // A table's <caption> stands above its grid in a {pdf}, in the caption's style, where
  // it was left out: the rows were read, the caption never.

  $pdf = padPdf ( '<table><caption>Table 1: Sales by month</caption><thead><tr><th>M</th><th>Amount</th></tr></thead>'
                . '<tbody><tr><td>Jan</td><td>12</td></tr></tbody></table>', [ 'compress' => FALSE ] );

  preg_match_all ( '#\((.*?)\) Tj#', $pdf, $found );

  $texts = implode ( ' / ', $found [1] );

?>
