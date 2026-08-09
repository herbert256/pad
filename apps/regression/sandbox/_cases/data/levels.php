<?php

  // Where a level's occurrences come from, which is the manual's "Levels and occurrences" page.
  // It shows one table built twelve ways, and the point of it is that they are all the same
  // shape underneath: a level with occurrences, nested inside another.
  //
  // The forms the other groups already assert are not repeated here - json, csv and xml have
  // data.php, the sequence subsystem has its own group, and a while loop has tags/constructs.php.
  // These are the ones nothing had.

  return [

    // Two independent stores nested. The inner one restarts for every occurrence of the outer,
    // which is what makes a table out of two flat lists.

    [ 'a level inside a level, from two separate stores',
      <<<'PAD'
      {data 'r'}(1,2){/data}
      {data 'c'}(1,2,3){/data}
      {r}
        [{c}{$c}{/c}]
      {/r}
      PAD,
      '[123][123]' ],

    // One nested array, walked by its two field names.

    [ 'a level inside a level, from one nested array',
      <<<'PAD'
      {rows}
        [{cols}{$cols}{/cols}]
      {/rows}
      PAD,
      '[12][34]',
      [ 'rows' => [
          [ 'cols' => [ 1, 2 ] ],
          [ 'cols' => [ 3, 4 ] ]
        ] ] ],

    // The same name at both levels, which the manual gives as one of the twelve. The inner tag
    // finds the row's own array rather than the outer store, so the name can be reused.

    [ 'the same name at both levels',
      <<<'PAD'
      {short}
        [{short}{$short}{/short}]
      {/short}
      PAD,
      '[1112][2122]',
      [ 'short' => [ [ 11, 12 ], [ 21, 22 ] ] ] ],

    // The data tag takes a second parameter naming a file in _data/, which is the twelfth way and
    // the only one that reads the occurrences from outside the page. The store is named by the
    // first parameter; the field keeps the name the file gives it.

    [ 'the data tag reads a file named as its second parameter',
      <<<'PAD'
      {data 'fromFile', 'nums.json'}
      {fromFile}
        {$nums},
      {/fromFile}
      PAD,
      '11,22,33,' ],

    // A sequence with nothing but a count is the shortest way to say "this many occurrences",
    // which is what the manual uses where the values do not matter.

    [ 'a bare count is a sequence of that many',
      <<<'PAD'
      {sequence 3}x{/sequence}
      PAD,
      'xxx' ],

    [ 'and it numbers them from one when given a name',
      <<<'PAD'
      {sequence 3, name='n'}
        {$n},
      {/sequence}
      PAD,
      '1,2,3,' ],

  ];

?>