<?php

  // mid - MID(text, start, count) is count characters of a text from the start-th on, the first
  // being 1; a start below 1 or a count below 0 is #VALUE!.
  //
  //   =MID("Spreadsheet", 3, 4)     read
  //   =MID("abc", 5, 2)

  return [
    'group' => 'Text',
    'args'  => [ 3, 3 ],
    'call'  => function ( $args, $env ) {
      $text  = padFormulaArgText ( $args [0], $env );
      $start = padFormulaArgInt ( $args [1], $env );
      $count = padFormulaArgInt ( $args [2], $env );
      if ( $error = padFormulaFirstError ( $text, $start, $count ) )
        return $error;
      if ( $start < 1 or $count < 0 )
        return padFormulaError ( '#VALUE!' );
      return mb_substr ( $text, $start - 1, $count );
    }
  ];

?>
