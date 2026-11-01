<?php

  // replace - REPLACE(text, start, count, new) puts a new text in the place of count characters of
  // a text from the start-th on, the first being 1 - a count of 0 inserts it, a start past the end
  // adds it at the end. A start below 1 or a count below 0 is #VALUE!, and so is a text that would
  // hold more than 32767 characters. A number is replaced in as the text it shows.
  //
  //   =REPLACE("abcdefghijk", 6, 5, "*")    abcde*k
  //   =REPLACE(2009, 3, 2, "10")            2010
  //   =REPLACE(A2, 1, 1, "Crab a")          Crab apples
  //   =REPLACE("abc", 0, 1, "x")            #VALUE!

  return [
    'group' => 'Text',
    'args'  => [ 4, 4 ],
    'call'  => function ( $args, $env ) {
      $text  = padFormulaArgText ( $args [0], $env );
      $start = padFormulaArgInt ( $args [1], $env );
      $count = padFormulaArgInt ( $args [2], $env );
      $new   = padFormulaArgText ( $args [3], $env );
      if ( $error = padFormulaFirstError ( $text, $start, $count, $new ) )
        return $error;
      if ( $start < 1 or $count < 0 )
        return padFormulaError ( '#VALUE!' );
      $head = mb_substr ( $text, 0, $start - 1 );
      $tail = mb_substr ( $text, $start - 1 + min ( $count, padFormulaMaxText + 1 ) );
      return padFormulaTooLong ( $head, $new, $tail ) ? padFormulaError ( '#VALUE!' ) : $head . $new . $tail;
    }
  ];

?>
