<?php

  // same:field - the same text as another field of the post, a password typed twice. The
  // other field needs no rules of its own; it is found where PHP files it, user[pass] too.
  // The field needs a name - same: with none failed every value, "must be the same as ", and
  // is the author's error.

  return [
    'check'    => function ( $value, $arg, $data ) {
      $other = padFormFind ( $data, $arg ) [1] ?? '';
      return is_scalar ( $other ) and $value === trim ( (string) $other );
    },
    'argument' => fn ( $arg ) => trim ( $arg ) !== '' ? '' : 'no field name - same:password',
    'fields'   => fn ( $arg ) => [ $arg ],
    'shown'    => fn ( $arg ) => padValidateName ( $arg ),
    'message'  => ':label must be the same as :n'
  ];

?>
