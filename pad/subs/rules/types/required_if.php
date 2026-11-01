<?php

  // required_if:field,value[,value...] - required, as required judges it, when another field
  // holds one of the values, as written - case and all, as in: compares: an address when
  // delivery is ship, required_if:delivery,ship,courier for either. The other field is read
  // as padRuleSibling reads it, user[kind] too, its text trimmed; a list (kinds[]) meets the
  // condition when one of its items does. A field the post leaves out holds nothing, and
  // nothing is none of the values - the field is not required then. Its argument is a field
  // name and a value: required_if:delivery, with none never required anything, and is the
  // author's error.

  return [
    'empty'    => TRUE,
    'check'    => fn ( $value, $arg, $data ) => ! padRuleSiblingIs ( $data, $arg ) or padRuleFilled ( $value ),
    'list'     => fn ( $list, $arg, $data ) => ! padRuleSiblingIs ( $data, $arg ) or padRuleFilled ( $list ),
    'argument' => fn ( $arg ) => ( padRuleFieldValues ( $arg ) [0] !== '' and padRuleFieldValues ( $arg ) [1] ) ? '' : 'no field name and value - required_if:delivery,ship',
    'shown'    => fn ( $arg ) => padRuleShownIs ( $arg ),
    'message'  => ':label is required when :n'
  ];

?>
