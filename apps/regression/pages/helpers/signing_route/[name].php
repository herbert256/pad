<?php

  // The route page a signed link of signing_encodes_the_page_in_the_link leads to: the
  // segment it was asked by, and whether the signature holds. Asked by its own name it is
  // no page.

  $routeSigned = "$name " . ( padSignatureValid () ? 'valid' : 'invalid' );

?>
