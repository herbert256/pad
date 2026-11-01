<?php

  // not - NOT(logical) turns TRUE into FALSE and FALSE into TRUE - 0 is FALSE, another number
  // TRUE.
  //
  //   =NOT(TRUE)                    FALSE
  //   =NOT(0)                       TRUE

  return [
    'group' => 'Logic',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $value = padFormulaArgBool ( $args [0], $env );
      return padFormulaIsError ( $value ) ? $value : ! $value;
    }
  ];

?>
