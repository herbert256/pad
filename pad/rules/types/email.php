<?php

  // email - an e-mail address as PHP's FILTER_VALIDATE_EMAIL takes one; the browser's half
  // carries the same pattern.

  return [
    'check'   => fn ( $value ) => filter_var ( $value, FILTER_VALIDATE_EMAIL ) !== FALSE,
    'message' => ':label must be a valid e-mail address'
  ];

?>
