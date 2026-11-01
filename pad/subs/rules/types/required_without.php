<?php

  // required_without:field[,field...] - required, as required judges it, when one of the
  // other fields is empty - one of two ways to be reached, a phone when there is no e-mail,
  // required_without:email. Empty is what required refuses: a text that is empty once
  // trimmed, a list without an item, a field the post leaves out. Its argument is a field
  // name - required_without: with none never required anything, and is the author's error.

  return [
    'empty'    => TRUE,
    'check'    => fn ( $value, $arg, $data ) => padRuleFilled ( $value ) or ! padRuleAny ( $data, $arg, FALSE ),
    'list'     => fn ( $list, $arg, $data ) => padRuleFilled ( $list ) or ! padRuleAny ( $data, $arg, FALSE ),
    'argument' => fn ( $arg ) => padRuleTexts ( $arg ) ? '' : 'no field name - required_without:email',
    'fields'   => fn ( $arg ) => padRuleTexts ( $arg ),
    'shown'    => fn ( $arg ) => implode ( ' or ', array_map ( 'padValidateName', padRuleTexts ( $arg ) ) ),
    'message'  => ':label is required when :n is empty'
  ];

?>
