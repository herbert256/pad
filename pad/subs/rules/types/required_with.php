<?php

  // required_with:field[,field...] - required, as required judges it, when one of the other
  // fields is filled, as required judges that: a text that is not empty once trimmed, a list
  // with an item filled in. A phone number with a country code, required_with:phone. A field
  // the post leaves out is read from the query string, as the page reads it, and is empty
  // when that has none either. Its argument is a field name - required_with: with none never
  // required anything, and is the author's error.

  return [
    'empty'    => TRUE,
    'check'    => fn ( $value, $arg, $data ) => padRuleFilled ( $value ) or ! padRuleAny ( $data, $arg, TRUE ),
    'list'     => fn ( $list, $arg, $data ) => padRuleFilled ( $list ) or ! padRuleAny ( $data, $arg, TRUE ),
    'argument' => fn ( $arg ) => padRuleTexts ( $arg ) ? '' : 'no field name - required_with:phone',
    'fields'   => fn ( $arg ) => padRuleTexts ( $arg ),
    'shown'    => fn ( $arg ) => implode ( ' or ', array_map ( 'padValidateName', padRuleTexts ( $arg ) ) ),
    'message'  => ':label is required when :n is filled in'
  ];

?>
