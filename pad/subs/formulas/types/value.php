<?php

  // value - VALUE(text) is the number a text writes - 1,250.50, 12%, $4, a date or a time; a text
  // that writes none is #VALUE!.
  //
  //   =VALUE("1,250.50")            1250.5
  //   =VALUE("12%")                 0.12
  //   =VALUE("2024-01-15")          45306
  //   =VALUE("abc")                 #VALUE!

  return [
    'group' => 'Text',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $value = padFormulaScalar ( $args [0], $env );
      if ( padFormulaIsError ( $value ) or is_float ( $value ) or is_int ( $value ) )
        return $value;
      if ( $value === NULL )
        return 0.0;
      if ( is_bool ( $value ) )
        return padFormulaError ( '#VALUE!' );
      return padFormulaParseValue ( (string) $value ) ?? padFormulaError ( '#VALUE!' );
    }
  ];

?>
