<?php

  // max_items:n - a list (a field named toppings[]) of at most n values: three toppings at
  // most, max_items:3. The values counted are those filled in, as min_items counts them
  // (padRuleItems); a field of one value holds one. Its argument is a whole number from 1 -
  // max_items:three is the author's error.

  return [
    'check'    => fn ( $value, $arg ) => 1 <= (int) trim ( $arg ),
    'list'     => fn ( $list, $arg ) => padRuleItems ( $list ) <= (int) trim ( $arg ),
    'argument' => fn ( $arg ) => preg_match ( '/^[1-9][0-9]*$/D', trim ( $arg ) ) ? '' : 'no whole number from 1 - max_items:3',
    'shown'    => fn ( $arg ) => trim ( $arg ),
    'message'  => fn ( $arg ) => ( trim ( $arg ) === '1' ) ? ':label must hold at most :n value' : ':label must hold at most :n values'
  ];

?>
