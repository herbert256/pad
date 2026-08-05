<?php

  // Implements quote="x": wraps the content in the quote character on both sides, so each
  // printed occurrence comes out quoted. Reached only from options/print.php.

  $padContent = padTagParm ('quote') . $padContent . padTagParm ('quote');

?>