<?php

  // min:n - at least n characters, counted as mb_strlen counts them; on a number field - one
  // with numeric or integer - a value of at least n. Its argument is a number - min:abc, or
  // min: with none, passed every value, and is the author's error.

  return [
    'check'    => fn ( $value, $arg, $data, $number ) => padValidateSize ( $value, $number ) >= (float) $arg,
    'argument' => fn ( $arg ) => padRuleNumber ( $arg ) ? '' : 'no number - min:3',
    'message'  => fn ( $arg, $number ) => $number ? ':label must be at least :n' : ':label must be at least :n characters'
  ];

?>
