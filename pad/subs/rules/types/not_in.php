<?php

  // not_in:a,b,c - none of the values of the list, as written: not_in:admin,root refuses
  // root and takes Root, as in: reads its list. A list field (tags[]) passes when no item it
  // holds is one of them - each a text. The list needs a value - not_in: with none refused
  // nothing, and is the author's error.

  return [
    'check'    => fn ( $value, $arg ) => ! in_array ( $value, array_map ( 'trim', explode ( ',', $arg ) ), TRUE ),
    'list'     => fn ( $list, $arg, $data, $number ) => count ( $list ) == count ( array_filter ( $list, fn ( $one ) =>
                    is_string ( $one ) and padValidateRule ( 'not_in', $arg, trim ( $one ), $data, $number ) ) ),
    'argument' => fn ( $arg ) => padRuleTexts ( $arg ) ? '' : 'no list - not_in:admin,root',
    'shown'    => fn ( $arg ) => implode ( ', ', array_map ( 'trim', explode ( ',', $arg ) ) ),
    'message'  => ':label must not be one of :n'
  ];

?>
