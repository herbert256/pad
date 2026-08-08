<?php

  // The generation options, and the invariants they promise.
  //
  // rows asks for a count, from and to bound the loop, minimal and maximal bound the values,
  // stop names a value to end on, skip discards opening candidates, unique drops repeats and
  // increment sets the step. Most of these are checked here as invariants - the right number
  // of terms, nothing outside the bounds - rather than as a literal list, so the case says
  // what the option means rather than what one sequence happened to do.

  return [

    [ 'rows gives exactly that many',
      <<<'PAD'
      {sequence prime, rows=6}
        {$sequence},
      {/sequence}
      PAD,
      '2,3,5,7,11,13,' ],

    [ 'from and to bound a predicate type by value',
      <<<'PAD'
      {sequence prime, from=10, to=30}
        {$sequence},
      {/sequence}
      PAD,
      '11,13,17,19,23,29,' ],

    [ 'from and to bound a formula type by position',
      <<<'PAD'
      {sequence square, from=1, to=5}
        {$sequence},
      {/sequence}
      PAD,
      '1,4,9,16,25,' ],

    [ 'sole asks for one value',
      <<<'PAD'
      {sequence prime, sole=7}
        {$sequence},
      {/sequence}
      PAD,
      '7,' ],

    [ 'sole that is not a member gives nothing',
      <<<'PAD'
      {sequence prime, sole=8}
        {$sequence},
      {/sequence}
      PAD,
      '' ],

    [ 'minimal bounds the values from below',
      <<<'PAD'
      {sequence prime, minimal=10, rows=4}
        {$sequence},
      {/sequence}
      PAD,
      '11,13,17,19,' ],

    [ 'maximal bounds the values from above',
      <<<'PAD'
      {sequence prime, maximal=10, rows=9}
        {$sequence},
      {/sequence}
      PAD,
      '2,3,5,7,' ],

    [ 'stop ends on the first term that reaches the value it names',
      <<<'PAD'
      {sequence prime, stop=11}
        {$sequence},
      {/sequence}
      PAD,
      '2,3,5,7,11,' ],

    [ 'increment steps the loop',
      <<<'PAD'
      {sequence loop, from=1, rows=5, increment=3}
        {$sequence},
      {/sequence}
      PAD,
      '1,4,7,10,13,' ],

    [ 'increment below 1 produces nothing rather than looping for ever',
      <<<'PAD'
      {sequence loop, from=1, to=10, increment=0}
        {$sequence},
      {/sequence}
      PAD,
      '' ],

    [ 'unique drops repeats',
      <<<'PAD'
      {sequence repeat=5, rows=4, unique}
        {$sequence},
      {/sequence}
      PAD,
      '5,' ],

    [ 'skip counts candidates offered, not terms kept',
      <<<'PAD'
      {sequence prime, skip=3, rows=5}
        {$sequence},
      {/sequence}
      PAD,
      '5,7,11,13,17,' ],

    [ 'skip on an unfiltered loop drops that many terms',
      <<<'PAD'
      {sequence loop, skip=3, rows=5}
        {$sequence},
      {/sequence}
      PAD,
      '4,5,6,7,8,' ],

    [ 'try limits the candidates weighed',
      <<<'PAD'
      {sequence strong, rows=4, try=50000}
        {$sequence},
      {/sequence}
      PAD,
      '1,2,145,40585,' ],

    [ 'randomly draws one term per position, all inside the window',
      <<<'PAD'
      {sequence loop, from=1, to=5, rows=20, randomly}
        {$sequence},
      {/sequence}
      PAD,
      '/^([1-5],){5}$/' ],

    [ 'randomly over a store draws from the store',
      <<<'PAD'
      {sequence '7;8;9', push='r'/}
      {sequence pull='r', randomly}
        {$sequence},
      {/sequence}
      PAD,
      '/^([789],){3}$/' ],

    [ 'randomly can reach the first term of a store',
      <<<'PAD'
      {sequence list='7', push='r1'/}
      {sequence pull='r1', randomly}
        {$sequence},
      {/sequence}
      PAD,
      '7,' ],

    [ 'a range literal counts up',
      <<<'PAD'
      {sequence '1..5'}
        {$sequence},
      {/sequence}
      PAD,
      '1,2,3,4,5,' ],

    [ 'a letter range counts up',
      <<<'PAD'
      {sequence 'A..E'}
        {$sequence},
      {/sequence}
      PAD,
      'A,B,C,D,E,' ],

    [ 'a letter range with an increment',
      <<<'PAD'
      {sequence 'A..K', increment=2}
        {$sequence},
      {/sequence}
      PAD,
      'A,C,E,G,I,K,' ],

    [ 'an increment wider than the range is capped to its span',
      <<<'PAD'
      {sequence '1..3', increment=5}
        {$sequence},
      {/sequence}
      PAD,
      '1,3,' ],

    [ 'a letter increment wider than the range is capped the same way',
      <<<'PAD'
      {sequence 'A..C', increment=5}
        {$sequence},
      {/sequence}
      PAD,
      'A,C,' ],

    [ 'a range with a zero increment still counts by one',
      <<<'PAD'
      {sequence range='1..5', increment=0}
        {$sequence},
      {/sequence}
      PAD,
      '1,2,3,4,5,' ],

    [ 'a list literal',
      <<<'PAD'
      {sequence '5;2;8'}
        {$sequence},
      {/sequence}
      PAD,
      '5,2,8,' ],

    [ 'a bare number asks for that many rows',
      <<<'PAD'
      {sequence 4}
        {$sequence},
      {/sequence}
      PAD,
      '1,2,3,4,' ],

  ];

?>