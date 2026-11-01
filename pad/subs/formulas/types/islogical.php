<?php

  // islogical - ISLOGICAL(value) is TRUE when the value is TRUE or FALSE - a comparison answers
  // one - and FALSE for anything else: the text "TRUE" too.
  //
  //   =ISLOGICAL(TRUE)              TRUE
  //   =ISLOGICAL(B2>5)              TRUE
  //   =ISLOGICAL("TRUE")            FALSE

  return [
    'group' => 'Information',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      return is_bool ( padFormulaScalar ( $args [0], $env ) );
    }
  ];

?>
