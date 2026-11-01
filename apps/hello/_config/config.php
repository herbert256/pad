<?php

  // Hello World is the page pairing at its plainest: index.php sets $message, index.pad
  // shows it. The document around it - with the brand every PAD application shows top left,
  // {common:padBrand/} - is the application's own _inits.pad and _exits.pad, so _common
  // stays off: under _common's wrapper it was a document inside a document, Tidy pulled it
  // apart and the page's own <title> was lost.

  $padCommon = FALSE;

?>
