<?php

  // The page a signed link leads to: it says whether the request carries a valid,
  // unexpired signature. Fetched by the suite itself, unsigned, it is invalid.

  $signedValid = padSignatureValid () ? 'valid' : 'invalid';

?>
