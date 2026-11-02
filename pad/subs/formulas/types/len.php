<?php

  // len - LEN(text) is the number of characters of a text - of a number as it reads in text.
  //
  //   =LEN("Spreadsheet")           11
  //   =LEN(1/4)                     4

  return [
    'group' => 'Text',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $text = padFormulaArgText ( $args [0], $env );
      return padFormulaIsError ( $text ) ? $text : (float) mb_strlen ( $text );
    }
  ];

?>
