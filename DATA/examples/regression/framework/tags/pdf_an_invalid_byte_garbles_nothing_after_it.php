<?php

  // One byte that is no UTF-8 in what a {pdf} renders - a raw value from the request, an
  // included file in Latin-1 - becomes one replacement, and the text after it stays as it
  // is written, where libxml read the rest of the document as Latin-1: été was Ã©tÃ©.

  $pdf  = padPdf ( "<p>caf\xE9</p><p>after: été</p>", [ 'compress' => FALSE ] );
  $kept = str_contains ( $pdf, "after: \xE9t\xE9" ) ? 'the text after it kept' : 'the text after it garbled';

?>
