<?php

  // A document that is new on every fetch - the source the curl ttl cases fetch twice and
  // expect the same answer from.

  $padContentType = 'application/json';

  echo json_encode ( [ [ 'stamp' => hrtime ( TRUE ), 'source' => 'misc/stamp' ] ], JSON_UNESCAPED_SLASHES );

?>
