<?php

  // left - LEFT(text, [count]) is the first characters of a text, one unless a count is given; a
  // count below 0 is #VALUE!.
  //
  //   =LEFT("Spreadsheet", 6)       Spread
  //   =LEFT("Spreadsheet")          S

  return [
    'group' => 'Text',
    'args'  => [ 1, 2 ],
    'call'  => function ( $args, $env ) {
      $text  = padFormulaArgText ( $args [0], $env );
      $count = isset ( $args [1] ) ? padFormulaArgInt ( $args [1], $env ) : 1;
      if ( $error = padFormulaFirstError ( $text, $count ) )
        return $error;
      return $count < 0 ? padFormulaError ( '#VALUE!' ) : mb_substr ( $text, 0, $count );
    }
  ];

?>
