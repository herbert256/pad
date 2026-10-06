<?php

  // Implements quote="x": wraps the content in the quote character on both sides, so each
  // printed occurrence comes out quoted. Reached only from options/print.php.

  // A value goes in as text under $padProtectValues - see options/close.php.

  $padOptQuote = $padProtectValues ? padProtect ( padTagParm ('quote') ) : padTagParm ('quote');
  $padContent  = $padOptQuote . $padContent . $padOptQuote;

?>
