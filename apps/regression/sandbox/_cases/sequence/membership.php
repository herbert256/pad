<?php

  // Asking whether a value belongs to a sequence, rather than asking for the sequence.
  //
  // Membership and generation are answered by different code, so they can disagree - and did.
  // Each pair below asks the same question both ways: keep over a range has to return exactly
  // the terms the type generates inside that range.
  //
  // Two failures worth keeping a case for. A type with a precomputed table was answered from
  // the table alone, so a value past its last entry was reported absent - 10001 squared was
  // not a square. And three types shipped an empty table, which by the same route made every
  // membership test false, so {keep nand} was always empty.

  return [

    [ 'keep matches generation - prime',
      <<<'PAD'
      {sequence loop, from=1, to=30, keep='prime'}
        {$sequence},
      {/sequence}
      PAD,
      '2,3,5,7,11,13,17,19,23,29,' ],

    [ 'keep matches generation - square',
      <<<'PAD'
      {sequence loop, from=1, to=30, keep='square'}
        {$sequence},
      {/sequence}
      PAD,
      '1,4,9,16,25,' ],

    [ 'keep matches generation - fibonacci',
      <<<'PAD'
      {sequence loop, from=1, to=30, keep='fibonacci'}
        {$sequence},
      {/sequence}
      PAD,
      '1,2,3,5,8,13,21,' ],

    [ 'keep matches generation - triangular',
      <<<'PAD'
      {sequence loop, from=1, to=30, keep='triangular'}
        {$sequence},
      {/sequence}
      PAD,
      '1,3,6,10,15,21,28,' ],

    [ 'keep matches generation - lucky',
      <<<'PAD'
      {sequence loop, from=1, to=25, keep='lucky'}
        {$sequence},
      {/sequence}
      PAD,
      '1,3,7,9,13,15,21,25,' ],

    [ 'keep matches generation - moserdebruijn',
      <<<'PAD'
      {sequence loop, from=1, to=25, keep='moserdebruijn'}
        {$sequence},
      {/sequence}
      PAD,
      '1,4,5,16,17,20,21,' ],

    [ 'keep matches generation - emirp',
      <<<'PAD'
      {sequence loop, from=1, to=120, keep='emirp'}
        {$sequence},
      {/sequence}
      PAD,
      '13,17,31,37,71,73,79,97,107,113,' ],

    [ 'keep matches generation - catalan',
      <<<'PAD'
      {sequence loop, from=1, to=50, keep='catalan'}
        {$sequence},
      {/sequence}
      PAD,
      '1,2,5,14,42,' ],

    [ 'keep matches generation - happy',
      <<<'PAD'
      {sequence loop, from=1, to=32, keep='happy'}
        {$sequence},
      {/sequence}
      PAD,
      '1,7,10,13,19,23,28,31,32,' ],

    [ 'remove is the complement of keep',
      <<<'PAD'
      {sequence loop, from=1, to=12, remove='prime'}
        {$sequence},
      {/sequence}
      PAD,
      '1,4,6,8,9,10,12,' ],

    [ 'a value inside the precomputed table',
      <<<'PAD'
      {sequence square, keep, sole=100000000}
        {$sequence},
      {/sequence}
      PAD,
      '100000000,' ],

    [ 'a value past the precomputed table is still recognised',
      <<<'PAD'
      {sequence square, keep, sole=100020001}
        {$sequence},
      {/sequence}
      PAD,
      '100020001,' ],

    [ 'a value past the table that does not belong',
      <<<'PAD'
      {sequence square, keep, sole=100020002}
        {$sequence},
      {/sequence}
      PAD,
      '' ],

    [ 'bitwise membership - and',
      <<<'PAD'
      {sequence loop, from=1, rows=3, keep='and|12'}
        {$sequence},
      {/sequence}
      PAD,
      '4,8,12,' ],

    [ 'bitwise membership - or',
      <<<'PAD'
      {sequence loop, from=1, rows=4, keep='or|5'}
        {$sequence},
      {/sequence}
      PAD,
      '5,7,13,15,' ],

    [ 'bitwise membership - nand runs negative',
      <<<'PAD'
      {sequence loop, from=-8, rows=2, keep='nand|12'}
        {$sequence},
      {/sequence}
      PAD,
      '-5,-1,' ],

    [ 'bitwise membership - xnor flags',
      <<<'PAD'
      {sequence loop, from=1, rows=6, flag='xnor|3'}
        {$sequence},
      {/sequence}
      PAD,
      '0,0,0,0,0,0,' ],

    [ 'bitwise membership - xor flags',
      <<<'PAD'
      {sequence loop, from=1, rows=6, flag='xor|3'}
        {$sequence},
      {/sequence}
      PAD,
      '1,1,0,1,1,1,' ],

    [ 'zero is a member of even',
      <<<'PAD'
      {sequence even, from=0, rows=4}
        {$sequence},
      {/sequence}
      PAD,
      '0,2,4,6,' ],

    [ 'zero is a member of pronic',
      <<<'PAD'
      {sequence pronic, from=0, rows=4}
        {$sequence},
      {/sequence}
      PAD,
      '0,2,6,12,' ],

    [ 'zero is not a member of happy',
      <<<'PAD'
      {sequence happy, from=0, rows=4}
        {$sequence},
      {/sequence}
      PAD,
      '1,7,10,13,' ],

    [ 'zero is not a member of antiprime',
      <<<'PAD'
      {sequence antiprime, from=0, rows=4}
        {$sequence},
      {/sequence}
      PAD,
      '1,2,4,6,' ],

    [ 'a play over a range that includes zero terminates',
      <<<'PAD'
      {sequence loop, from=0, rows=4, keep='power|2'}
        {$sequence},
      {/sequence}
      PAD,
      '2,4,8,16,' ],

  ];

?>