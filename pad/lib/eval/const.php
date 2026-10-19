<?php

  // The evaluator's operator tables, shared by the scanner, the checks and the parser.
  //
  //   padEval_precedence  every operator; a word not in this list is a candidate function
  //                       name
  //   padEval_bp          the binding power of each operator with a value on both sides,
  //                       which the parser (lib/eval/tree.php) applies
  //   padEval_1           single-character operators the scanner recognises - and ** as two
  //   padEval_txt         word operators, matched case-insensitively
  //   padEval_alt         symbol spellings mapped onto their word form, so <, <=, =, ==,
  //                       <> and != all reduce to LT/LE/EQ/NE
  //   padEval_one         the prefix operators, which take one value on their right. NEG and
  //                       POS are the signs before a value that is no digit (-$x, -(...));
  //                       only the scanner makes them, never a word
  //   padEval_tests       the pipe functions that read infix as a test

  const padEval_precedence = [
    '!',
    '**', '*', '/', '%', '+', '-',
    '.',
    'LT', 'LE', 'GT', 'GE', 'EQ', 'NE',
    'NOT',
    'AND', 'XOR', 'OR',
    '??',
  ];

  // The binding power of the operators with a value on both sides, the stronger the higher.
  // The operators of one power are taken left to right - 10 - 2 + 3 is 11 - except **, which
  // binds right to left: 2 ** 3 ** 2 is 2 ** 9. ?? binds weakest - $a ?? $b . 'x' is
  // $a ?? ($b . 'x') - and the inline ternary cond ? a : b weaker still.
  //
  // The prefix operators stand between them: the signs and ! at 110, weaker than ** and
  // stronger than the rest, as in PHP - -$b ** 2 is -($b ** 2), and 2 ** -$b is 2 ** (-$b);
  // not at 50, between the comparisons and and, where Python has it - not $a eq $b denies the
  // comparison, $a and not $b denies $b alone. A test written infix - $s in ('a', 'b') - takes
  // its input at 75, the arithmetic and the concatenation on its left: $n + 1 range (9, 9).

  const padEval_bp = [
    '??' => 10,
    'OR' => 20, 'XOR' => 30, 'AND' => 40,
    'EQ' => 60, 'NE'  => 60,
    'LT' => 70, 'LE'  => 70, 'GT'  => 70, 'GE' => 70,
    '.'  => 80,
    '+'  => 90, '-'  => 90,
    '*'  => 100, '/' => 100, '%' => 100,
    '**' => 120,
  ];

  const padEval_1   = [ '!', '+', '-', '*', '/', '%', '.' ];
  const padEval_txt = [ 'LT', 'LE', 'GT', 'GE', 'EQ', 'NE', 'AND', 'XOR', 'OR', 'NOT' ];
  const padEval_one = [ 'NOT', '!', 'NEG', 'POS' ];

  // The pipe functions that read infix as a test - {if $s in ('a', 'b')}, $s not like 'A%' -
  // the value on their left being their input.

  const padEval_tests = [ 'in', 'like', 'matches', 'range', 'between', 'contains' ];

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
