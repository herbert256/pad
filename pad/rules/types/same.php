<?php

  // same:field - the same text as another field of the post, a password typed twice. The
  // other field needs no rules of its own; it is found where PHP files it, user[pass] too.

  return [
    'check'   => function ( $value, $arg, $data ) {
      $other = padFormFind ( $data, $arg ) [1] ?? '';
      return is_scalar ( $other ) and $value === trim ( (string) $other );
    },
    'shown'   => fn ( $arg ) => padValidateName ( $arg ),
    'message' => ':label must be the same as :n'
  ];

?>
