<?php

  // lte:other - at most another field, or a number written in the rule: no more guests than
  // seats, lte:seats. Measured as gt: measures (padRuleCompare) - the value on a number field,
  // else the length - and an other field that is empty, a list or on a number field no
  // number lets it pass. Its argument is a number or a field name - lte: with none is the
  // author's error.

  return [
    'check'    => fn ( $value, $arg, $data, $number ) => ( $two = padRuleCompare ( $value, $arg, $data, $number ) ) === NULL or $two [0] <= $two [1],
    'argument' => fn ( $arg ) => trim ( $arg ) !== '' ? '' : 'no number or field name - lte:100 or lte:seats',
    'fields'   => fn ( $arg ) => padRuleNumber ( $arg ) ? [] : [ $arg ],
    'shown'    => fn ( $arg ) => padRuleNumber ( $arg ) ? trim ( $arg ) : padValidateName ( $arg ),
    'message'  => fn ( $arg, $number ) => $number ? ':label must be at most :n'
                                                  : ( padRuleNumber ( $arg ) ? ':label must be at most :n characters' : ':label must be at most as long as :n' )
  ];

?>
