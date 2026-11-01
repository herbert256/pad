<?php

  // right - RIGHT(text, [count]) is the last characters of a text, one unless a count is given; a
  // count below 0 is #VALUE!.
  //
  //   =RIGHT("Spreadsheet", 5)      sheet
  //   =RIGHT(2024, 2)               24

  return [
    'group' => 'Text',
    'args'  => [ 1, 2 ],
    'call'  => function ( $args, $env ) {
      $text  = padFormulaArgText ( $args [0], $env );
      $count = isset ( $args [1] ) ? padFormulaArgInt ( $args [1], $env ) : 1;
      if ( $error = padFormulaFirstError ( $text, $count ) )
        return $error;
      if ( $count < 0 )
        return padFormulaError ( '#VALUE!' );
      return $count == 0 ? '' : mb_substr ( $text, max ( 0, mb_strlen ( $text ) - $count ) );
    }
  ];

?>
