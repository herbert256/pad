<?php

  // min:n - at least n characters, counted as mb_strlen counts them; on a number field - one
  // with numeric or integer - a value of at least n.

  return [
    'check'   => fn ( $value, $arg, $data, $number ) => padValidateSize ( $value, $number ) >= (float) $arg,
    'message' => fn ( $arg, $number ) => $number ? ':label must be at least :n' : ':label must be at least :n characters'
  ];

?>
