<?php

  // A JSON document of the query values a and b it was asked with - the fixture that shows
  // which address a fetch really asked for.

  $padContentType = 'application/json';

  echo json_encode ( [ [ 'a' => (string) ( $_GET ['a'] ?? '' ), 'b' => (string) ( $_GET ['b'] ?? '' ) ] ] );

?>
