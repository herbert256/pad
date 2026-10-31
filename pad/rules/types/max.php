<?php

  // max:n - at most n characters, or on a number field a value of at most n.

  return [
    'check'   => fn ( $value, $arg, $data, $number ) => padValidateSize ( $value, $number ) <= (float) $arg,
    'message' => fn ( $arg, $number ) => $number ? ':label must be at most :n' : ':label must be at most :n characters'
  ];

?>
