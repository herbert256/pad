<?php

  // The products whose name, category or text holds the query. The same PHP serves the page
  // and every answer to htmx: the input's hx-target="#results" sends HX-Target: results,
  // and PAD answers the {fragment 'results'} of search.pad alone. The query stands in the
  // address (hx-push-url), so a reload - or a link - shows the same results whole.

  $q = trim ( (string) padRequest ( 'q', '' ) );

  $found = array_values ( array_filter ( htmxData ( 'products' ), fn ( $product ) =>
             $q === '' or stripos ( $product ['name'] . ' ' . $product ['category'] . ' ' . $product ['blurb'], $q ) !== FALSE ) );

  $count = count ( $found );

?>
