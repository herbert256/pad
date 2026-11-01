<?php

  // rept - REPT(text, times) is a text repeated a number of times - cut to a whole number toward 0,
  // 0 the empty text; below 0 it is #VALUE!, and so is a text that would hold more than 32767
  // characters, which is never built.
  //
  //   =REPT("*-", 3)                *-*-*-
  //   =REPT("-", 10)                ----------
  //   =REPT(A6, 2)                  PlumsPlums
  //   =REPT("x", -1)                #VALUE!

  return [
    'group' => 'Text',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $text  = padFormulaArgText ( $args [0], $env );
      $times = padFormulaArgInt ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $text, $times ) )
        return $error;
      if ( $times < 0 )
        return padFormulaError ( '#VALUE!' );
      if ( $text === '' or $times == 0 )
        return '';
      if ( $times > padFormulaMaxText or mb_strlen ( $text ) * $times > padFormulaMaxText )
        return padFormulaError ( '#VALUE!' );
      return str_repeat ( $text, $times );
    }
  ];

?>
