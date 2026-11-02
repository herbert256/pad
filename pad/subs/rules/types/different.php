<?php

  // different:field - not the same text as another field of the post, the mirror of same: a
  // new password that is not the one in use, different:current_password. The other field is
  // found where PHP files it, as same: finds it - user[pass] too - its text trimmed; one the
  // post leaves out, an empty one or a list holds no text the value could be, so the value
  // differs from it. The field needs a name - different: with none is the author's error.

  return [
    'check'    => function ( $value, $arg, $data ) {
      $other = padFormFind ( $data, $arg ) [1] ?? '';
      return ! ( is_scalar ( $other ) and $value === trim ( (string) $other ) );
    },
    'argument' => fn ( $arg ) => trim ( $arg ) !== '' ? '' : 'no field name - different:current_password',
    'fields'   => fn ( $arg ) => [ $arg ],
    'shown'    => fn ( $arg ) => padValidateName ( $arg ),
    'message'  => ':label must be different from :n'
  ];

?>
