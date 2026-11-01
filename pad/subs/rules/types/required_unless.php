<?php

  // required_unless:field,value[,value...] - required, as required judges it, unless another
  // field holds one of the values, as written: an address unless delivery is collect. The
  // other field is read as required_if reads it - a list holds a value when one of its items
  // does - and a field the post leaves out holds none of them, so the field is required
  // then. Its argument is a field name and a value: required_unless:delivery, with none
  // always required the field, and is the author's error.

  return [
    'empty'    => TRUE,
    'check'    => fn ( $value, $arg, $data ) => padRuleSiblingIs ( $data, $arg ) or padRuleFilled ( $value ),
    'list'     => fn ( $list, $arg, $data ) => padRuleSiblingIs ( $data, $arg ) or padRuleFilled ( $list ),
    'argument' => fn ( $arg ) => ( padRuleFieldValues ( $arg ) [0] !== '' and padRuleFieldValues ( $arg ) [1] ) ? '' : 'no field name and value - required_unless:delivery,collect',
    'shown'    => fn ( $arg ) => padRuleShownIs ( $arg ),
    'message'  => ':label is required unless :n'
  ];

?>
