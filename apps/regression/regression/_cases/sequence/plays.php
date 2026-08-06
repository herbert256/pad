<?php

  // The four play modes, in each of the ways they can be written.
  //
  // make, keep, remove and flag can arrive as an option with a value, as a bare option on the
  // sequence itself, as the tag name, or through either half of a prefix. All of those are
  // meant to mean the same thing. remove and flag did not: every form except the first quietly
  // did what keep does, so a page could show four "equivalent" rows of which three were wrong.
  //
  // The reference row of each block is the option-with-a-value form; the rest have to match it.

  return [

    [ 'keep, option with a value',
      <<<'PAD'
      {sequence loop, rows=12, from=10, keep='prime'}
        {$sequence},
      {/sequence}
      PAD,
      '11,13,17,19,23,29,31,37,41,43,47,53,' ],

    [ 'keep, bare option on the type',
      <<<'PAD'
      {sequence prime, rows=12, from=10, keep}
        {$sequence},
      {/sequence}
      PAD,
      '11,13,17,19,23,29,31,37,41,43,47,53,' ],

    [ 'keep, as the tag',
      <<<'PAD'
      {keep prime, rows=12, from=10}
        {$sequence},
      {/keep}
      PAD,
      '11,13,17,19,23,29,31,37,41,43,47,53,' ],

    [ 'keep, prefixed mode:type',
      <<<'PAD'
      {keep:prime rows=12, from=10}
        {$sequence},
      {/keep:prime}
      PAD,
      '11,13,17,19,23,29,31,37,41,43,47,53,' ],

    [ 'keep, prefixed type:mode',
      <<<'PAD'
      {prime:keep rows=12, from=10}
        {$sequence},
      {/prime:keep}
      PAD,
      '11,13,17,19,23,29,31,37,41,43,47,53,' ],

    [ 'remove, option with a value',
      <<<'PAD'
      {sequence loop, rows=12, from=10, remove='prime'}
        {$sequence},
      {/sequence}
      PAD,
      '10,12,14,15,16,18,20,21,22,24,25,26,' ],

    [ 'remove, bare option on the type',
      <<<'PAD'
      {sequence prime, rows=12, from=10, remove}
        {$sequence},
      {/sequence}
      PAD,
      '10,12,14,15,16,18,20,21,22,24,25,26,' ],

    [ 'remove, as the tag',
      <<<'PAD'
      {remove prime, rows=12, from=10}
        {$sequence},
      {/remove}
      PAD,
      '10,12,14,15,16,18,20,21,22,24,25,26,' ],

    [ 'remove, prefixed mode:type',
      <<<'PAD'
      {remove:prime rows=12, from=10}
        {$sequence},
      {/remove:prime}
      PAD,
      '10,12,14,15,16,18,20,21,22,24,25,26,' ],

    [ 'remove, prefixed type:mode',
      <<<'PAD'
      {prime:remove rows=12, from=10}
        {$sequence},
      {/prime:remove}
      PAD,
      '10,12,14,15,16,18,20,21,22,24,25,26,' ],

    [ 'flag, option with a value',
      <<<'PAD'
      {sequence loop, rows=12, from=10, flag='prime'}
        {$sequence},
      {/sequence}
      PAD,
      '0,1,0,1,0,0,0,1,0,1,0,0,' ],

    [ 'flag, bare option on the type',
      <<<'PAD'
      {sequence prime, rows=12, from=10, flag}
        {$sequence},
      {/sequence}
      PAD,
      '0,1,0,1,0,0,0,1,0,1,0,0,' ],

    [ 'flag, as the tag',
      <<<'PAD'
      {flag prime, rows=12, from=10}
        {$sequence},
      {/flag}
      PAD,
      '0,1,0,1,0,0,0,1,0,1,0,0,' ],

    [ 'flag, prefixed mode:type',
      <<<'PAD'
      {flag:prime rows=12, from=10}
        {$sequence},
      {/flag:prime}
      PAD,
      '0,1,0,1,0,0,0,1,0,1,0,0,' ],

    [ 'flag, prefixed type:mode',
      <<<'PAD'
      {prime:flag rows=12, from=10}
        {$sequence},
      {/prime:flag}
      PAD,
      '0,1,0,1,0,0,0,1,0,1,0,0,' ],

    [ 'make rewrites the terms',
      <<<'PAD'
      {sequence loop, rows=5, make='multiply|3'}
        {$sequence},
      {/sequence}
      PAD,
      '3,6,9,12,15,' ],

    [ 'make with a negation play',
      <<<'PAD'
      {sequence pell, to=5, rows=3, make='negation'}
        {$sequence},
      {/sequence}
      PAD,
      '-1,-2,-5,' ],

    [ 'a play over a parameterised type',
      <<<'PAD'
      {sequence loop, from=6, rows=8, remove='power|2'}
        {$sequence},
      {/sequence}
      PAD,
      '6,7,9,10,11,12,13,14,' ],

    [ 'negative does not invert a play - remove does',
      <<<'PAD'
      {sequence loop, from=1, to=10, keep='prime', negative}
        {$sequence},
      {/sequence}
      PAD,
      '2,3,5,7,' ],

  ];

?>