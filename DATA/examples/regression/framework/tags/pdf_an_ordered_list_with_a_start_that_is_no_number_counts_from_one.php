<?php

  // An ordered list of a {pdf} whose start= is no number counts from 1, as a browser and
  // the preview sheet do - start="" and start="x" counted from 0 - and a start with a sign,
  // spaces or a tail after its digits is read as HTML reads it.

  $pdf = padPdf ( '<ol start=""><li>one</li><li>two</li></ol><ol start="x"><li>three</li></ol>'
                . '<ol start=" +4 "><li>four</li></ol><ol start="7th"><li>seven</li></ol><ol start="-2"><li>minus</li></ol>', [ 'compress' => FALSE ] );

  preg_match_all ( '#\(([^)]*)\) Tj#', $pdf, $found );

  $texts = implode ( ' ', $found [1] );

?>
