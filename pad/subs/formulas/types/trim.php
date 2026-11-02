<?php

  // trim - TRIM(text) takes the spaces off both ends of a text and leaves one space where there
  // were more between its words - " a " & " " & " b " is " a   b ", and trimmed a b.
  //
  //   =TRIM(" a "&" "&" b ")        a b

  return [
    'group' => 'Text',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $text = padFormulaArgText ( $args [0], $env );
      return padFormulaIsError ( $text ) ? $text : preg_replace ( '/ {2,}/', ' ', trim ( $text, ' ' ) );
    }
  ];

?>
