<?php

  // in:a,b,c - one of the values of the list, as written: in:red,green refuses Red. A list
  // field (tags[]) passes when every item it holds is one of them - each a text.

  return [
    'check'   => fn ( $value, $arg ) => in_array ( $value, array_map ( 'trim', explode ( ',', $arg ) ), TRUE ),
    'list'    => fn ( $list, $arg, $data, $number ) => count ( $list ) == count ( array_filter ( $list, fn ( $one ) =>
                   is_string ( $one ) and padValidateRule ( 'in', $arg, trim ( $one ), $data, $number ) ) ),
    'shown'   => fn ( $arg ) => implode ( ', ', array_map ( 'trim', explode ( ',', $arg ) ) ),
    'message' => ':label must be one of :n'
  ];

?>
