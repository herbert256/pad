<?php

  // lt:other - less than another field, or than a number written in the rule: a discount
  // below the price, lt:price, a share below the whole, lt:100. Measured as gt: measures
  // (padRuleCompare) - the value on a number field, else the length - and an other field
  // that is empty, a list or on a number field no number lets it pass. Its argument is a
  // number or a field name - lt: with none is the author's error.

  return [
    'check'    => fn ( $value, $arg, $data, $number ) => ( $two = padRuleCompare ( $value, $arg, $data, $number ) ) === NULL or $two [0] < $two [1],
    'argument' => fn ( $arg ) => trim ( $arg ) !== '' ? '' : 'no number or field name - lt:100 or lt:price',
    'fields'   => fn ( $arg ) => padRuleNumber ( $arg ) ? [] : [ $arg ],
    'shown'    => fn ( $arg ) => padRuleNumber ( $arg ) ? trim ( $arg ) : padValidateName ( $arg ),
    'message'  => fn ( $arg, $number ) => $number ? ':label must be less than :n'
                                                  : ( padRuleNumber ( $arg ) ? ':label must be shorter than :n characters' : ':label must be shorter than :n' )
  ];

?>
