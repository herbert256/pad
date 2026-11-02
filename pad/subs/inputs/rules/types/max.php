<?php

  // max:n - at most n characters, or on a number field a value of at most n. Its argument is a
  // number - max:abc failed every value, "must be at most abc characters", and is the
  // author's error.

  return [
    'check'    => fn ( $value, $arg, $data, $number ) => padValidateSize ( $value, $number ) <= (float) $arg,
    'argument' => fn ( $arg ) => padRuleNumber ( $arg ) ? '' : 'no number - max:255',
    'message'  => fn ( $arg, $number ) => $number ? ':label must be at most :n' : ':label must be at most :n characters'
  ];

?>
