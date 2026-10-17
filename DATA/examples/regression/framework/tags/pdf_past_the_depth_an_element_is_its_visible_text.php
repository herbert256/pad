<?php

  // Past the depth the walk goes in a {pdf}, an element is set as the text it shows: the
  // code of a <script> and the rules of a <style> in it stay out, as they do above that
  // depth, and a block ends its line - where its textContent wrote "alert(1).x{color:red}"
  // into the document and ran two paragraphs together as one word, "FirstSecond".

  $html = str_repeat ( '<div>', 300 )
        . 'DEEP <b>bold</b><script>alert(1)</script><style>.x{color:red}</style><p>First</p><p>Second</p>'
        . str_repeat ( '</div>', 300 ) . '<p>AFTER</p>';

  preg_match_all ( '#\((.*?)\) Tj#', padPdf ( $html, [ 'compress' => FALSE ] ), $found );

  $lines = implode ( '|', $found [1] );

?>
