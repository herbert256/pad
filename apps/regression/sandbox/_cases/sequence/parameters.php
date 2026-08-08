<?php

  // How a sequence type's parameter may be written.
  //
  // Every form here was broken at some point by a change meant to harden the parameter
  // handling, and none of them appears on a page of this application - which is exactly why
  // the breakage reached the repository unnoticed. A parameter can be a plain number, a range
  // to draw from once per term, the name of a stored sequence to take a term from per term,
  // or - for chance alone - a percentage.
  //
  // The random forms are pinned by shape rather than by value.

  return [

    [ 'plain number',
      <<<'PAD'
      {sequence add=3, rows=4}
        {$sequence},
      {/sequence}
      PAD,
      '4,5,6,7,' ],

    [ 'bare, no parameter',
      <<<'PAD'
      {sequence add, rows=3}
        {$sequence},
      {/sequence}
      PAD,
      '2,3,4,' ],

    [ 'range parameter, drawn per term',
      <<<'PAD'
      {sequence add='1..5', rows=4}
        {$sequence},
      {/sequence | steps('add', 1, 5, 4)}
      PAD,
      'ok' ],

    [ 'three-dot range parameter',
      <<<'PAD'
      {sequence add='1...5', rows=4}
        {$sequence},
      {/sequence | steps('add', 1, 5, 4)}
      PAD,
      'ok' ],

    [ 'range parameter as a play',
      <<<'PAD'
      {sequence loop, rows=4, make='add|1..5'}
        {$sequence},
      {/sequence | steps('add', 1, 5, 4)}
      PAD,
      'ok' ],

    [ 'parameter naming a stored sequence',
      <<<'PAD'
      {sequence '7;8;9', push='p'/}
      {sequence add='p', rows=3}
        {$sequence},
      {/sequence}
      PAD,
      '8,10,12,' ],

    [ 'multiply with a range parameter',
      <<<'PAD'
      {sequence multiply='2..4', rows=4}
        {$sequence},
      {/sequence | steps('multiply', 2, 4, 4)}
      PAD,
      'ok' ],

    [ 'chance as a percentage',
      <<<'PAD'
      {sequence chance='100%', rows=4}
        {$sequence},
      {/sequence}
      PAD,
      '1,2,3,4,' ],

    [ 'chance one-in-one',
      <<<'PAD'
      {sequence chance=1, rows=4}
        {$sequence},
      {/sequence}
      PAD,
      '1,2,3,4,' ],

    [ 'multiple with a range parameter',
      <<<'PAD'
      {sequence multiple='2..4', rows=4}
        {$sequence},
      {/sequence | steps('ceil', 2, 4, 4)}
      PAD,
      'ok' ],

    [ 'divide with a range parameter that can draw zero',
      <<<'PAD'
      {sequence divide='0..3', rows=4}
        {$sequence},
      {/sequence | steps('divide', 0, 3, 4)}
      PAD,
      'ok' ],

    [ 'fractional parameter, ceil',
      <<<'PAD'
      {sequence ceil=0.5, from=1, to=3}
        {$sequence},
      {/sequence}
      PAD,
      '1,2,3,' ],

    [ 'fractional parameter, floor',
      <<<'PAD'
      {sequence floor=0.5, from=1, to=3}
        {$sequence},
      {/sequence}
      PAD,
      '1,2,3,' ],

    [ 'fractional parameter, modulo',
      <<<'PAD'
      {sequence modulo=0.5, from=1, to=3}
        {$sequence},
      {/sequence}
      PAD,
      '0,0,0,' ],

    [ 'negative parameter',
      <<<'PAD'
      {sequence add=-2, rows=4}
        {$sequence},
      {/sequence}
      PAD,
      '-1,0,1,2,' ],

  ];

?>