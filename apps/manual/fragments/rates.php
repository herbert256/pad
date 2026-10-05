<?php

  // The remote source of the remote data page: a JSON document, as an API would answer.

  $padContentType = 'application/json';

  echo json_encode ( [ [ 'code' => 'EUR', 'rate' => 1.0 ], [ 'code' => 'USD', 'rate' => 1.08 ], [ 'code' => 'GBP', 'rate' => 0.86 ] ] );

?>
