<?php

  // The evaluator's operator tables, shared by the tokeniser, padEvalAfter and padEvalOpr.
  //
  //   padEval_precedence  every operator in the order padEvalOpr applies them, strongest
  //                       first; a word not in this list is a candidate function name
  //   padEval_1           single-character operators the tokeniser recognises
  //   padEval_2           two-character operators, tested before the single-character ones
  //   padEval_txt         word operators, matched case-insensitively
  //   padEval_alt         symbol spellings mapped onto their word form, so <, <=, =, ==,
  //                       <> and != all reduce to LT/LE/EQ/NE
  //   padEval_one         the unary operators - the ones eval/go/go.php runs with a right
  //                       operand only
  //
  // padEval_s lists the arithmetic and concatenation operators but currently has no reader.

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
  const padEval_s   = [ '+', '-', '*', '/', '%', '.' ];
  const padEval_one = ['not','!'];

  const padEval_alt = [
    '<'  => 'LT',
    '<=' => 'LE',
    '>'  => 'GT',
    '>=' => 'GE',
    '='  => 'EQ',
    '==' => 'EQ',
    '<>' => 'NE',
    '!=' => 'NE'
  ];

?>