<?php

  // gte:other - at least another field, or a number written in the rule: a highest price
  // no lower than the lowest, gte:min_price. Measured as gt: measures (padRuleCompare) - the
  // value on a number field, else the length - and an other field that is empty, a list or
  // on a number field no number lets it pass. Its argument is a number or a field name -
  // gte: with none is the author's error.

  return [
    'check'    => fn ( $value, $arg, $data, $number ) => ( $two = padRuleCompare ( $value, $arg, $data, $number ) ) === NULL or $two [0] >= $two [1],
    'argument' => fn ( $arg ) => trim ( $arg ) !== '' ? '' : 'no number or field name - gte:0 or gte:min_price',
    'fields'   => fn ( $arg ) => padRuleNumber ( $arg ) ? [] : [ $arg ],
    'shown'    => fn ( $arg ) => padRuleNumber ( $arg ) ? trim ( $arg ) : padValidateName ( $arg ),
    'message'  => fn ( $arg, $number ) => $number ? ':label must be at least :n'
                                                  : ( padRuleNumber ( $arg ) ? ':label must be at least :n characters' : ':label must be at least as long as :n' )
  ];

?>
