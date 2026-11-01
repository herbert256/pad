<?php

  // min_words:n - a text of at least n words: a review of twenty words or more,
  // min_words:20. A word is a run of characters between white space (padRuleWords) - the
  // spaces, tabs and line breaks and the other white space of Unicode, the no-break space
  // among them - so e-mail is one word and "a - b" three; a text that is no UTF-8 fails it.
  // An empty field is judged by required alone. Its argument is a whole number from 1 -
  // min_words:many is the author's error.

  return [
    'check'    => fn ( $value, $arg ) => ( $words = padRuleWords ( $value ) ) !== NULL and $words >= (int) trim ( $arg ),
    'argument' => fn ( $arg ) => preg_match ( '/^[1-9][0-9]*$/D', trim ( $arg ) ) ? '' : 'no whole number from 1 - min_words:20',
    'shown'    => fn ( $arg ) => trim ( $arg ),
    'message'  => fn ( $arg ) => ( trim ( $arg ) === '1' ) ? ':label must be at least :n word' : ':label must be at least :n words'
  ];

?>
