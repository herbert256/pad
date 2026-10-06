<?php

  $title = "Everything";

  // A bracketed route - products/[id] - is reached through a path that fills in its value,
  // never by its own name, and {ajax} under the strict check refuses a page it cannot find:
  // the first one in the list ended this page with a 500.

  foreach ( padAppsList () as $key => $one ) {
    if ( str_contains ( $one ['item'], '[' ) )
      continue;
    $items [$key] ['app']  = $one ['app'];
    $items [$key] ['item'] = $one ['item'];
  }

  ksort ($items);

?>
