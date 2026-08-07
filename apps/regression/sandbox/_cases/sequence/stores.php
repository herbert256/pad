<?php

  // Storing a sequence and reading it back.
  //
  // A run is kept under the name it was given, and a later tag names it to pull it out again.
  // The trap is that a name given with name= was only stored when the tag was written without
  // a closing tag: closing it silently cost the store, and reading it back then produced the
  // counting sequence a name nothing recognises falls back to - which looks enough like an
  // answer to pass unnoticed.
  //
  // A name that matches nothing at all is an error rather than that counter, so the cases
  // that would once have shown 1,2,3,... are gone from here by construction.

  return [

    [ 'push then pull',
      <<<'PAD'
      {sequence loop, from=11, to=14, push='s'/}
      {sequence s}
        {$sequence},
      {/sequence}
      PAD,
      '11,12,13,14,' ],

    [ 'push on a closed tag',
      <<<'PAD'
      {sequence loop, from=11, to=14, push='s'}
      {/sequence}
      {pull:s}
        {$sequence},
      {/pull:s}
      PAD,
      '11,12,13,14,' ],

    [ 'name on a self-closing tag',
      <<<'PAD'
      {sequence loop, from=11, to=14, name='s'}
      {sequence s}
        {$sequence},
      {/sequence}
      PAD,
      '11,12,13,14,' ],

    [ 'name on a closed tag stores it too',
      <<<'PAD'
      {sequence loop, from=11, to=14, name='s'}
      {/sequence}
      {sequence s}
        {$sequence},
      {/sequence}
      PAD,
      '11,12,13,14,' ],

    [ 'the name publishes the value under itself',
      <<<'PAD'
      {sequence loop, from=5, to=7, name='n'}
        {$n},
      {/sequence}
      PAD,
      '5,6,7,' ],

    [ 'a pulled sequence keeps its values',
      <<<'PAD'
      {sequence '1..5', push='a'/}
      {pull:a}
        {$sequence},
      {/pull:a}
      PAD,
      '1,2,3,4,5,' ],

    [ 'pulling twice gives the same answer',
      <<<'PAD'
      {sequence '1..4', push='a'/}
      {pull:a}
        {$sequence},
      {/pull:a}
      {pull:a}
        {$sequence},
      {/pull:a}
      PAD,
      '1,2,3,4,1,2,3,4,' ],

    [ 'a store holds what the actions produced',
      <<<'PAD'
      {sequence '1..5', reverse, push='a'/}
      {pull:a}
        {$sequence},
      {/pull:a}
      PAD,
      '5,4,3,2,1,' ],

    [ 'resume transforms the stored sequence',
      <<<'PAD'
      {sequence '1..4', push='a'/}
      {resume add=10}
      {pull:a}
        {$sequence},
      {/pull:a}
      PAD,
      '11,12,13,14,' ],

    [ 'resume twice',
      <<<'PAD'
      {sequence '1..3', push='a'/}
      {resume add=10}
      {resume reverse}
      {pull:a}
        {$sequence},
      {/pull:a}
      PAD,
      '13,12,11,' ],

    [ 'pushing under a second name copies it',
      <<<'PAD'
      {sequence '1..3', push='a'/}
      {pull:a push='b'/}
      {pull:b}
        {$sequence},
      {/pull:b}
      PAD,
      '1,2,3,' ],

    [ 'a store used as an action operand',
      <<<'PAD'
      {sequence '1..5', push='a'/}
      {sequence '4..8', push='b'/}
      {pull:a intersection='b'}
        {$sequence},
      {/pull:a}
      PAD,
      '4,5,' ],

  ];

?>