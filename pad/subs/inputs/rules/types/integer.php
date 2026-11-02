<?php

  // integer - a whole number PHP's integers hold, as FILTER_VALIDATE_INT reads one: no
  // leading zero, a sign allowed. Like numeric it makes the field a number.

  return [
    'numeric' => TRUE,
    'check'   => fn ( $value ) => filter_var ( $value, FILTER_VALIDATE_INT ) !== FALSE,
    'message' => ':label must be a whole number'
  ];

?>
