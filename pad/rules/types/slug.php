<?php

  // slug - the name of a page in an address, as padStrSlug makes one: lower case letters
  // and digits, words joined by one dash, no dash at either end.

  return [
    'check'   => fn ( $value ) => preg_match ( '/^[a-z0-9]+(-[a-z0-9]+)*$/D', $value ) === 1,
    'message' => ':label must be lower case letters and digits joined by dashes'
  ];

?>
