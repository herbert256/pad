<?php

  // The line break right after <pre> is no line, as HTML has it: a code block written as
  // <pre> and the code on the next line began one line lower in the PDF, under a blank one.

  $where = function ( $html ) {
    preg_match ( '#([\d.]+) Td \(line1\)#', padPdf ( $html, [ 'compress' => FALSE ] ), $found );
    return $found [1] ?? '?';
  };

  $same = $where ( "<p>before</p><pre>\nline1\nline2\n</pre>" ) === $where ( "<p>before</p><pre>line1\nline2\n</pre>" )
        ? 'at the same height' : 'one line lower';

?>
