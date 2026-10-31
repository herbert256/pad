<?php

  // numeric - a number as is_numeric reads one: 12, -1.5, 1e3, .5. It makes the field a
  // number, so min, max and between compare its value rather than its length.

  return [
    'numeric' => TRUE,
    'check'   => fn ( $value ) => is_numeric ( $value ),
    'message' => ':label must be a number'
  ];

?>
