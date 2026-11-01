<?php

  // max_words:n - a text of at most n words: an abstract of 250 words, max_words:250. Words
  // are counted as min_words counts them (padRuleWords), the runs of characters between
  // white space; a text that is no UTF-8 fails it. Its argument is a whole number from 1 -
  // max_words:many is the author's error.

  return [
    'check'    => fn ( $value, $arg ) => ( $words = padRuleWords ( $value ) ) !== NULL and $words <= (int) trim ( $arg ),
    'argument' => fn ( $arg ) => preg_match ( '/^[1-9][0-9]*$/D', trim ( $arg ) ) ? '' : 'no whole number from 1 - max_words:250',
    'shown'    => fn ( $arg ) => trim ( $arg ),
    'message'  => fn ( $arg ) => ( trim ( $arg ) === '1' ) ? ':label must be at most :n word' : ':label must be at most :n words'
  ];

?>
