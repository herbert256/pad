<?php

  // digits, digits:n - the digits 0 to 9 and nothing else, and with n exactly n of them: a
  // postal code of four, a PIN of six. Unlike integer it keeps a leading zero - 0042 is four
  // digits - and makes no number field, so min and max still count characters.

  return [
    'check'    => fn ( $value, $arg ) => preg_match ( '/^[0-9]+$/D', $value ) === 1
                                         and ( trim ( $arg ) === '' or strlen ( $value ) == (int) trim ( $arg ) ),
    'argument' => fn ( $arg ) => ( trim ( $arg ) === '' or preg_match ( '/^[1-9][0-9]*$/D', trim ( $arg ) ) ) ? '' : 'no length - digits:4',
    'shown'    => fn ( $arg ) => trim ( $arg ),
    'message'  => fn ( $arg ) => ( trim ( $arg ) === '' ) ? ':label must contain digits only' : ':label must be :n digits'
  ];

?>
