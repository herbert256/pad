<?php

  // A link's address goes into the PDF as the browser follows the same href: a byte past
  // ASCII or a space percent-encoded, a backslash of a web address a slash - they were left
  // out, and the link to /€ü went to the site's root, "d e" to "de".

  $pdf  = padPdf ( '<p><a href="https://ex.com/€ü">one</a> <a href="https://ex.com/c\d e">two</a></p>', [ 'compress' => FALSE ] );

  preg_match_all ( '#/URI \(([^)]*)\)#', $pdf, $found );

  $uris = implode ( ' ', $found [1] );

?>
