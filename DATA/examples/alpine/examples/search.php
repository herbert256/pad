<?php

  // The page renders the first results into the component's argument; every later query
  // asks the same page with padFormat=json, and the same PHP answers - the templates never
  // run for that request, and only $result leaves the server ($padExpose).

  $q = trim ( (string) padRequest ( 'q', '' ) );

  $found = array_values ( array_filter ( alpineData ( 'products' ), fn ( $product ) =>
             $q === '' or stripos ( $product ['name'] . ' ' . $product ['category'] . ' ' . $product ['blurb'], $q ) !== FALSE ) );

  $result = [ 'q' => $q, 'count' => count ( $found ), 'items' => array_slice ( $found, 0, 8 ) ];

  $padExpose = [ 'result' ];

?>
