<?php

  // A second remote source for the parallel fetching page: a JSON document, as an API
  // would answer.

  $padContentType = 'application/json';

  echo json_encode ( [ [ 'city' => 'Amsterdam', 'sky' => 'clouds' ], [ 'city' => 'Lisbon', 'sky' => 'sun' ] ] );

?>
