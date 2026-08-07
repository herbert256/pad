<?php

  // Arithmetic, printf formats and dates.
  //
  // The arithmetic pipes need a space between the operator and the operand. A fixed timestamp is
  // used rather than the current time so the answers can be stated; now is pinned by shape, being
  // different on every run.

  return [

    [ 'add, note the space before the operand',
      <<<'PAD'
      {echo 5 | + 1}
      PAD,
      '6' ],

    [ 'subtract',
      <<<'PAD'
      {echo 5 | - 2}
      PAD,
      '3' ],

    [ 'multiply',
      <<<'PAD'
      {echo 5 | * 2}
      PAD,
      '10' ],

    [ 'divide keeps the fraction',
      <<<'PAD'
      {echo 10 | / 4}
      PAD,
      '2.5' ],

    [ 'arithmetic applies to a zero value',
      <<<'PAD'
      {echo 0 | + 5}
      PAD,
      '5' ],

    [ 'arithmetic chains',
      <<<'PAD'
      {echo 5 | + 1 | * 2}
      PAD,
      '12' ],

    [ 'printf, fixed decimals',
      <<<'PAD'
      {echo 3.14159265 | %.5f}
      PAD,
      '3.14159' ],

    [ 'printf, one decimal on an integer',
      <<<'PAD'
      {echo 42 | %.1f}
      PAD,
      '42.0' ],

    [ 'printf, integer',
      <<<'PAD'
      {echo 42 | %d}
      PAD,
      '42' ],

    [ 'printf, hexadecimal',
      <<<'PAD'
      {echo 42 | %x}
      PAD,
      '2a' ],

    [ 'printf, zero padded',
      <<<'PAD'
      {echo 42 | %05d}
      PAD,
      '00042' ],

    [ 'printf, signed',
      <<<'PAD'
      {echo 42 | %+d}
      PAD,
      '+42' ],

    [ 'printf, width',
      <<<'PAD'
      {echo 3.14159 | %8.3f}
      PAD,
      '   3.142' ],

    [ 'date formats a timestamp',
      <<<'PAD'
      {echo 1702483200 | date('Y-m-d')}
      PAD,
      '2023-12-13' ],

    [ 'date takes a strtotime modifier',
      <<<'PAD'
      {echo 1702483200 | date('Y-m-d', '+1 week')}
      PAD,
      '2023-12-20' ],

    [ 'date gives the short day name',
      <<<'PAD'
      {echo 1702483200 | date('D')}
      PAD,
      'Wed' ],

    [ 'date gives the full day name',
      <<<'PAD'
      {echo 1702483200 | date('l')}
      PAD,
      'Wednesday' ],

    [ 'time is the same function',
      <<<'PAD'
      {echo 1702483200 | time('Y')}
      PAD,
      '2023' ],

    [ 'timestamp is the same function',
      <<<'PAD'
      {echo 1702483200 | timestamp('Y')}
      PAD,
      '2023' ],

    [ 'now gives a unix timestamp',
      <<<'PAD'
      {echo 0 | now}
      PAD,
      '/^\\d{10}$/' ],

  ];

?>