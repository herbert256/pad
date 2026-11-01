<?php

  // min_items:n - a list (a field named toppings[]) of at least n values: two toppings or
  // more, min_items:2. The values counted are those filled in - an item left blank, one of
  // three fields of guests[] left empty, is none (padRuleItems) - and a field of one value
  // holds one. An empty list is judged by required alone, as min: leaves an empty text:
  // required|min_items:2. Its argument is a whole number from 1 - min_items:two is the
  // author's error.

  return [
    'check'    => fn ( $value, $arg ) => 1 >= (int) trim ( $arg ),
    'list'     => fn ( $list, $arg ) => padRuleItems ( $list ) >= (int) trim ( $arg ),
    'argument' => fn ( $arg ) => preg_match ( '/^[1-9][0-9]*$/D', trim ( $arg ) ) ? '' : 'no whole number from 1 - min_items:2',
    'shown'    => fn ( $arg ) => trim ( $arg ),
    'message'  => fn ( $arg ) => ( trim ( $arg ) === '1' ) ? ':label must hold at least :n value' : ':label must hold at least :n values'
  ];

?>
