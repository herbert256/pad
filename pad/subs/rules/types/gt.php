<?php

  // gt:other - more than another field, or than a number written in the rule: a highest
  // price above the lowest, gt:min_price, an amount above nothing, gt:0 - where min:0 takes
  // 0. What is compared is what min and max measure (padRuleCompare): on a number field -
  // one with numeric, integer or multiple_of - the value, on any other its length in
  // characters, the other field measured alike. An other field that is empty or a list - on
  // a number field one that is no number - lets it pass: its own rules speak. Its argument
  // is a number or a field name - gt: with none is the author's error.

  return [
    'check'    => fn ( $value, $arg, $data, $number ) => ( $two = padRuleCompare ( $value, $arg, $data, $number ) ) === NULL or $two [0] > $two [1],
    'argument' => fn ( $arg ) => trim ( $arg ) !== '' ? '' : 'no number or field name - gt:0 or gt:min_price',
    'fields'   => fn ( $arg ) => padRuleNumber ( $arg ) ? [] : [ $arg ],
    'shown'    => fn ( $arg ) => padRuleNumber ( $arg ) ? trim ( $arg ) : padValidateName ( $arg ),
    'message'  => fn ( $arg, $number ) => $number ? ':label must be greater than :n'
                                                  : ( padRuleNumber ( $arg ) ? ':label must be longer than :n characters' : ':label must be longer than :n' )
  ];

?>
