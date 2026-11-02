<?php

  // rand - RAND() is a number from 0 up to 1. A spreadsheet gives another on every computation;
  // here the number is the same for the same seed (the seed= of {sheet:grid}, 0 unless given),
  // cell and call - a page answers the same twice, so a stored answer holds.
  //
  //   =RAND()<1                     TRUE
  //   =RAND()>=0                    TRUE

  return [
    'group' => 'Math',
    'args'  => [ 0, 0 ],
    'call'  => function ( $args, &$env ) {
      return padFormulaRandom ( $env );
    }
  ];

?>
