<?php

  // A {form method='get'} keeps what was asked - its fields refill from the query string -
  // on a clean URL as on a ?page one. The query form's first name is the page, and is
  // passed over; on /routes/search?q=pad the page is the path, and the first name is the
  // field: it was passed over all the same, and the search box came back empty.

  $refillClean = padCurl ( $padHost . 'regression/pages/index.php/routes/search?q=pad&padInclude' ) ['data'] ?? '';
  $refillQuery = padCurl ( $padGoExt . 'routes/search&q=pad&padInclude' ) ['data'] ?? '';

  echo trim ( $refillClean ), ' | ', trim ( $refillQuery );

?>
