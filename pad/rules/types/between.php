<?php

  // between:a,b - min:a and max:b in one rule: from a to b characters long, or on a number
  // field a value from a to b. Its argument is two numbers, the lower first - between:10 or
  // between:9,1 would pass nothing or everything, and is the author's error.

  return [
    'check'    => function ( $value, $arg, $data, $number ) {
      [ $low, $high ] = array_map ( 'floatval', explode ( ',', $arg, 2 ) + [ 1 => '' ] );
      $size = padValidateSize ( $value, $number );
      return $size >= $low and $size <= $high;
    },
    'argument' => function ( $arg ) {
      $number = '\s*([+-]?(?:\d+(?:\.\d*)?|\.\d+))\s*';
      return ( preg_match ( "/^$number,$number$/D", $arg, $two ) and (float) $two [1] <= (float) $two [2] ) ? '' : 'no range - between:1,10';
    },
    'shown'    => fn ( $arg ) => implode ( ' and ', array_map ( 'trim', explode ( ',', $arg, 2 ) ) ),
    'message'  => fn ( $arg, $number ) => $number ? ':label must be between :n' : ':label must be between :n characters'
  ];

?>
