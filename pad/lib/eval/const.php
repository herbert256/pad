<?php

  // The evaluator's operator tables, shared by the tokeniser, padEvalAfter and padEvalOpr.
  //
  //   padEval_precedence  every operator, strongest first; a word not in this list is a
  //                       candidate function name
  //   padEval_groups      the same operators grouped by equal strength, which is what
  //                       padEvalOpr applies
  //   padEval_1           single-character operators the tokeniser recognises
  //   padEval_2           two-character operators, tested before the single-character ones
  //   padEval_txt         word operators, matched case-insensitively
  //   padEval_alt         symbol spellings mapped onto their word form, so <, <=, =, ==,
  //                       <> and != all reduce to LT/LE/EQ/NE
  //   padEval_one         the unary operators - the ones eval/go/go.php runs with a right
  //                       operand only. NEG and POS are the signs before a value that is no
  //                       digit (-$x, -(...)); only the tokeniser makes them, never a word
  //
  // ?? binds weakest of the binary operators - $a ?? $b . 'x' is $a ?? ($b . 'x') - and the
  // inline ternary cond ? a : b weaker still: padEvalTernary takes it before any group.
  //
  // padEval_s lists the arithmetic and concatenation operators but currently has no reader.

  const padEval_precedence = [
    '!',
    '**', '*', '/', '%', '+', '-',
    '.',
    'LT', 'LE', 'GT', 'GE', 'EQ', 'NE',
    'NOT',
    'AND', 'XOR', 'OR',
    '??',
  ];

  // The binding strength padEvalOpr applies, strongest first. The operators of one group
  // bind equally and are taken left to right - 10 - 2 + 3 is 11 - except ** , which binds
  // right to left: 2 ** 3 ** 2 is 2 ** 9. One operator per level, as the flat list above was
  // walked, did all the + before any -, so 10 - 2 + 3 came out 5. NOT stands between the
  // comparisons and AND, where Python has it: not $a eq $b denies the comparison, and
  // $a and not $b denies $b alone. The signs and ! bind weaker than ** and stronger than the
  // rest, as in PHP: -$b ** 2 is -($b ** 2), -9, with brackets around it or an operator
  // before it as well - (-$b ** 2) and 0 + -$b ** 2 were 9 while -$b ** 2 was -9. A sign or
  // a ! that stands as the right operand of ** is its own operand's first (padEvalOpr):
  // 2 ** -$b is 2 ** (-$b).

  const padEval_groups = [
    [ '**' ],
    [ '!', 'NEG', 'POS' ],
    [ '*', '/', '%' ],
    [ '+', '-' ],
    [ '.' ],
    [ 'LT', 'LE', 'GT', 'GE' ],
    [ 'EQ', 'NE' ],
    [ 'NOT' ],
    [ 'AND' ],
    [ 'XOR' ],
    [ 'OR' ],
    [ '??' ],
  ];

  const padEval_1   = [ '!', '+', '-', '*', '/', '%', '.' ];
  const padEval_2   = [ '**'];
  const padEval_txt = [ 'LT', 'LE', 'GT', 'GE', 'EQ', 'NE', 'AND', 'XOR', 'OR', 'NOT' ];
  const padEval_s   = [ '+', '-', '*', '/', '%', '.' ];
  // Written 'not' until now, which never matched: padEval_txt is matched case-insensitively but
  // stored uppercase, so the token eval/go/go.php tests here is NOT. The word spelling was
  // therefore not unary at all - it fell through to the binary path, where doubleVarVar.php has
  // no line for it either, and {echo '' | not 0} rendered 200.

  const padEval_one = ['NOT','!','NEG','POS'];

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
