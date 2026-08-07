<?php

  // The data handling options, which reshape a tag's data before it is walked. The manual's
  // "Data handling" page names fifteen of them and none had a case: dedup, end, first, last,
  // page, random, reverse, row, rows, shuffle, slice, splice, sort, start, trim, each with a
  // file of its own in pad/handling/types/, plus the negative modifier that inverts the choice
  // and the left/right pair that makes trim one-sided.
  //
  // Six of one list is enough to tell every option apart, so the data is [1,2,3,4,5,6] and each
  // case states what is left of it. Where an option counts from the end it is written negative,
  // which is why start=-2 and end=-2 are here beside their positive forms.
  //
  // random and shuffle draw a different answer each run, so those two cases assert how many rows
  // came back rather than which - one character per occurrence - which is the part of them that
  // is not chance.

  return [

    [ 'first takes one from the front',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n first}
        {$n},
      {/n}
      PAD,
      '1,' ],

    [ 'first with a count',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n first=2}
        {$n},
      {/n}
      PAD,
      '1,2,' ],

    [ 'last takes from the other end',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n last=2}
        {$n},
      {/n}
      PAD,
      '5,6,' ],

    [ 'negative keeps what the option would have dropped',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n first=2, negative}
        {$n},
      {/n}
      PAD,
      '3,4,5,6,' ],

    [ 'row picks one by its position',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n row=3}
        {$n},
      {/n}
      PAD,
      '3,' ],

    [ 'rows limits how many',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n rows=2}
        {$n},
      {/n}
      PAD,
      '1,2,' ],

    [ 'page moves along by that many',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n rows=2, page=3}
        {$n},
      {/n}
      PAD,
      '5,6,' ],

    [ 'start counts from the front',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n start=3}
        {$n},
      {/n}
      PAD,
      '3,4,5,6,' ],

    [ 'end stops there',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n end=3}
        {$n},
      {/n}
      PAD,
      '1,2,3,' ],

    [ 'start and end together',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n start=2, end=4}
        {$n},
      {/n}
      PAD,
      '2,3,4,' ],

    [ 'a negative start counts from the back',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n start=-2}
        {$n},
      {/n}
      PAD,
      '5,6,' ],

    [ 'and a negative end stops that far from it',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n end=-2}
        {$n},
      {/n}
      PAD,
      '1,2,3,4,' ],

    // slice keeps what it names and splice takes it out, which is the whole difference between
    // them. Both read a second number after a | as a length.

    [ 'slice keeps the first so many',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n slice=2}
        {$n},
      {/n}
      PAD,
      '1,2,' ],

    [ 'splice removes them instead',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n splice=2}
        {$n},
      {/n}
      PAD,
      '3,4,5,6,' ],

    [ 'slice from a position, for a length',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n slice='2|3'}
        {$n},
      {/n}
      PAD,
      '3,4,5,' ],

    [ 'splice from a position, for a length',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n splice='2|3'}
        {$n},
      {/n}
      PAD,
      '1,2,6,' ],

    [ 'trim takes one off each end',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n trim}
        {$n},
      {/n}
      PAD,
      '2,3,4,5,' ],

    [ 'trim with a count takes that many off each',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n trim=2}
        {$n},
      {/n}
      PAD,
      '3,4,' ],

    [ 'left trims only the front',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n trim=2, left}
        {$n},
      {/n}
      PAD,
      '3,4,5,6,' ],

    [ 'right trims only the back',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n trim=2, right}
        {$n},
      {/n}
      PAD,
      '1,2,3,4,' ],

    [ 'reverse turns it round',
      <<<'PAD'
      {data 'n'}
        [1,2,3,4,5,6]
      {/data}
      {n reverse}
        {$n},
      {/n}
      PAD,
      '6,5,4,3,2,1,' ],

    [ 'sort puts it in order',
      <<<'PAD'
      {data 'n'}
        [3,1,2]
      {/data}
      {n sort}
        {$n},
      {/n}
      PAD,
      '1,2,3,' ],

    [ 'dedup drops the repeats',
      <<<'PAD'
      {data 'n'}
        [1,1,2,2,3]
      {/data}
      {n dedup}
        {$n},
      {/n}
      PAD,
      '1,2,3,' ],

    [ 'random gives back as many as asked for',
      <<<'PAD'
      {data 'n'}
        [1,2,3]
      {/data}
      {n random=2}
        x
      {/n}
      PAD,
      'xx' ],

    [ 'shuffle keeps them all, in some order',
      <<<'PAD'
      {data 'n'}
        [1,2,3]
      {/data}
      {n shuffle}
        x
      {/n}
      PAD,
      'xxx' ],

  ];

?>