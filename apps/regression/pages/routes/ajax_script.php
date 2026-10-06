<?php

  // The page an {ajax} or a {page app=} loads in the browser goes into the script as one
  // JavaScript string. A route segment may hold a quote - products/[id] takes any value -
  // and the name went in as it was: this one closed the string and ran its own statement.

  $p = 'routes/products/x");alert(1);("';

?>
