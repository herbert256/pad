<?php

  // The parts of the sequence library that the engine calls from outside a sequence tag.
  //
  // pqTruncate() lives in pad/lib/sequence.php but is reached through the trim option on any
  // data tag, and a sequence tag never goes near it - its own trim action refuses a count of
  // zero before the library is called. So the case that catches a broken pqTruncate has to
  // iterate a plain list, which is what $seqFixture in index.php is for and why these cases
  // are rendered in this file's scope rather than sandboxed.
  //
  // Trimming nothing off the right once sliced the list to nothing, silently emptying the
  // data of any tag written with trim=0.

  return [

    [ 'a plain list iterates',
      <<<'PAD'
      {seqFixture}
        {$seqFixture},
      {/seqFixture}
      PAD,
      '1,2,3,4,5,', 'scope' ],

    [ 'trim takes one off each end',
      <<<'PAD'
      {seqFixture trim=1}
        {$seqFixture},
      {/seqFixture}
      PAD,
      '2,3,4,', 'scope' ],

    [ 'trim=0 takes nothing off',
      <<<'PAD'
      {seqFixture trim=0}
        {$seqFixture},
      {/seqFixture}
      PAD,
      '1,2,3,4,5,', 'scope' ],

    [ 'trim=0 on the right takes nothing off',
      <<<'PAD'
      {seqFixture trim=0, right}
        {$seqFixture},
      {/seqFixture}
      PAD,
      '1,2,3,4,5,', 'scope' ],

    [ 'trim=0 on the left takes nothing off',
      <<<'PAD'
      {seqFixture trim=0, left}
        {$seqFixture},
      {/seqFixture}
      PAD,
      '1,2,3,4,5,', 'scope' ],

    [ 'trim on the right only',
      <<<'PAD'
      {seqFixture trim=2, right}
        {$seqFixture},
      {/seqFixture}
      PAD,
      '1,2,3,', 'scope' ],

    [ 'trim on the left only',
      <<<'PAD'
      {seqFixture trim=2, left}
        {$seqFixture},
      {/seqFixture}
      PAD,
      '3,4,5,', 'scope' ],

  ];

?>