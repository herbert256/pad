<?php

  // The name a {pdf} is given keeps the letters of every script and its digits, where all
  // but ASCII went - Überblick was berblick.pdf, 発注書 document.pdf - and a slash of it is a
  // dash, where it threw away all before it: invoice-2026/114 was 114.pdf.

  $names = implode ( ' | ', array_map ( 'padPdfName', [ 'Factuur Müller', 'Überblick', '発注書', 'invoice-2026/114', '../../etc/passwd' ] ) );

?>
