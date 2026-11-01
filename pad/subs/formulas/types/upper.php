<?php

  // upper - UPPER(text) is a text in capitals.
  //
  //   =UPPER("pad sheet")           PAD SHEET

  return [
    'group' => 'Text',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $text = padFormulaArgText ( $args [0], $env );
      return padFormulaIsError ( $text ) ? $text : mb_strtoupper ( $text );
    }
  ];

?>
