<?php

  // The text of a PDF is composed first - an e and its accent apart, as macOS file names
  // and copy-paste give it, is the é WinAnsi has, where it was "e?" - and the characters
  // that draw nothing are left out: a byte order mark or a zero-width joiner was a "?".

  include_once PAD . 'tags/_lib/pdf.php';

  $text = mb_convert_encoding ( padPdfText ( "Cafe\u{0301} Zoe\u{0308} \u{FEFF}X \u{200D}Y" ), 'UTF-8', 'Windows-1252' );

?>
