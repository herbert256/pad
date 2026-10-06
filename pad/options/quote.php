<?php

  // Implements quote="x": wraps the content in the quote character on both sides, so each
  // printed occurrence comes out quoted. Reached only from options/print.php.

  // A value goes in as text, a written-out option as template text - see options/close.php.

  $padOptQuote = ( $padProtectValues and ! padOptionLiteral ( 'quote' ) ) ? padProtect ( padTagParm ('quote') ) : padTagParm ('quote');
  $padContent  = $padOptQuote . $padContent . $padOptQuote;

?>
