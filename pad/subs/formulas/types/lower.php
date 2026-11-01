<?php

  // lower - LOWER(text) is a text in small letters.
  //
  //   =LOWER("PAD Sheet")           pad sheet

  return [
    'group' => 'Text',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $text = padFormulaArgText ( $args [0], $env );
      return padFormulaIsError ( $text ) ? $text : mb_strtolower ( $text );
    }
  ];

?>
