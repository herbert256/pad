<?php
  
  const padEval_precedence = [
    '!',
    '**', '*', '/', '%', '+', '-',
    '.',
    'LT', 'LE', 'GT', 'GE', 'EQ', 'NE',
    'AND', 'XOR', 'OR',
    'NOT',
  ];

  const padEval_1   = [ '!', '+', '-', '*', '/', '%', '.' ];
  const padEval_2   = [ '**'];
  const padEval_txt = [ 'LT', 'LE', 'GT', 'GE', 'EQ', 'NE', 'AND', 'XOR', 'OR', 'NOT' ];
  
  const padEval_alt = [ 
    '<'  => 'LT', 
    '<=' => 'LE', 
    '>'  => 'GT', 
    '>=' => 'GE', 
    '='  => 'EQ', 
    '==' => 'EQ', 
    '<>' => 'NE'
  ];

?>