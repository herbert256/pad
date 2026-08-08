<?php

  // The 29 actions, each over a sequence whose answer can be stated.
  //
  // Most work on 1..10 or on a short unsorted list, so the answer is obvious by inspection.
  // The two-sequence actions use 1..6 against 4..9, which overlap without either containing
  // the other - a pair where difference, onlyNow and onlyStore all give different answers, so
  // one cannot pass for another.
  //
  // The empty-sequence and missing-store rows are the boundary cases: none of them may end
  // the request, and each has an answer it is supposed to give instead.

  return [

    [ 'reverse',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums reverse}
        {$sequence},
      {/pull:nums}
      PAD,
      '10,9,8,7,6,5,4,3,2,1,' ],

    [ 'sort',
      <<<'PAD'
      {sequence '9;1;5;3', push='mix'/}
      {pull:mix sort}
        {$sequence},
      {/pull:mix}
      PAD,
      '1,3,5,9,' ],

    [ 'shuffle keeps every value',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums shuffle, sort}
        {$sequence},
      {/pull:nums}
      PAD,
      '1,2,3,4,5,6,7,8,9,10,' ],

    [ 'randomize draws that many, in range, without repeats',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums randomize=3}
        {$sequence},
      {/pull:nums | draws(1, 10, 3, 'unique')}
      PAD,
      'ok' ],

    [ 'randomize orderly keeps the drawn values ascending',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums randomize=4, orderly}
        {$sequence},
      {/pull:nums | draws(1, 10, 4, 'unique', 'ascending')}
      PAD,
      'ok' ],

    [ 'the draws oracle rejects an impossible value',
      <<<'PAD'
      {echo '99,99,99,' | draws(1, 10, 3)}
      PAD,
      'out of range 1..10: 99' ],

    [ 'the draws oracle rejects a duplicate',
      <<<'PAD'
      {echo '5,5,7,' | draws(1, 10, 3, 'unique')}
      PAD,
      'duplicate draw: 5,5,7' ],

    [ 'the draws oracle rejects a disordered draw',
      <<<'PAD'
      {echo '9,1,8,2,' | draws(1, 10, 4, 'unique', 'ascending')}
      PAD,
      'not ascending: 9,1,8,2' ],


    [ 'first',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums first}
        {$sequence},
      {/pull:nums}
      PAD,
      '1,' ],

    [ 'first=3',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums first=3}
        {$sequence},
      {/pull:nums}
      PAD,
      '1,2,3,' ],

    [ 'last=3',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums last=3}
        {$sequence},
      {/pull:nums}
      PAD,
      '8,9,10,' ],

    [ 'first with negative inverts it',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums first=3, negative}
        {$sequence},
      {/pull:nums}
      PAD,
      '4,5,6,7,8,9,10,' ],

    [ 'element counts from 1',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums element=5}
        {$sequence},
      {/pull:nums}
      PAD,
      '5,' ],

    [ 'element past the end gives nothing',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums element=99}
        {$sequence},
      {/pull:nums}
      PAD,
      '' ],

    [ 'slice counts from 0',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums slice='3|4'}
        {$sequence},
      {/pull:nums}
      PAD,
      '4,5,6,7,' ],

    [ 'slice with no value leaves it alone',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums slice}
        {$sequence},
      {/pull:nums}
      PAD,
      '1,2,3,4,5,6,7,8,9,10,' ],

    [ 'splice removes',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums splice='2|3'}
        {$sequence},
      {/pull:nums}
      PAD,
      '1,2,6,7,8,9,10,' ],

    [ 'splice with a store that does not exist keeps the values',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums splice='1|nosuch'}
        {$sequence},
      {/pull:nums}
      PAD,
      '1,2,3,4,5,6,7,8,9,10,' ],


    [ 'minimum',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums minimum}
        {$sequence},
      {/pull:nums}
      PAD,
      '1,' ],

    [ 'minimum=3',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums minimum=3}
        {$sequence},
      {/pull:nums}
      PAD,
      '1,2,3,' ],

    [ 'maximum=3',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums maximum=3}
        {$sequence},
      {/pull:nums}
      PAD,
      '8,9,10,' ],

    [ 'sum',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums sum}
        {$sequence},
      {/pull:nums}
      PAD,
      '55,' ],

    [ 'product',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums product}
        {$sequence},
      {/pull:nums}
      PAD,
      '3628800,' ],

    [ 'count',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums count}
        {$sequence},
      {/pull:nums}
      PAD,
      '10,' ],

    [ 'average',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums average}
        {$sequence},
      {/pull:nums}
      PAD,
      '5.5,' ],

    [ 'median of an even count averages the two middles',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums median}
        {$sequence},
      {/pull:nums}
      PAD,
      '5.5,' ],

    [ 'median sorts first',
      <<<'PAD'
      {sequence '9;1;5;3', push='mix'/}
      {pull:mix median}
        {$sequence},
      {/pull:mix}
      PAD,
      '4,' ],

    [ 'distinct',
      <<<'PAD'
      {sequence '9;1;5;3', push='mix'/}
      {pull:mix distinct}
        {$sequence},
      {/pull:mix}
      PAD,
      '4,' ],

    [ 'dedup',
      <<<'PAD'
      {sequence '1;1;2;2;3', push='d'/}
      {pull:d dedup}
        {$sequence},
      {/pull:d}
      PAD,
      '1,2,3,' ],


    [ 'trim takes off both ends',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums trim=2}
        {$sequence},
      {/pull:nums}
      PAD,
      '3,4,5,6,7,8,' ],

    [ 'trim=0 takes off nothing',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums trim=0}
        {$sequence},
      {/pull:nums}
      PAD,
      '1,2,3,4,5,6,7,8,9,10,' ],

    [ 'trim left only',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums trim, left=8}
        {$sequence},
      {/pull:nums}
      PAD,
      '9,10,' ],

    [ 'trim value fills both, then left adds',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums trim=2, left=3}
        {$sequence},
      {/pull:nums}
      PAD,
      '6,7,8,' ],


    [ 'append',
      <<<'PAD'
      {sequence '1..6', push='a'/}
      {sequence '4..9', push='b'/}
      {pull:a append='b'}
        {$sequence},
      {/pull:a}
      PAD,
      '1,2,3,4,5,6,4,5,6,7,8,9,' ],

    [ 'prepend',
      <<<'PAD'
      {sequence '1..6', push='a'/}
      {sequence '4..9', push='b'/}
      {pull:a prepend='b'}
        {$sequence},
      {/pull:a}
      PAD,
      '4,5,6,7,8,9,1,2,3,4,5,6,' ],

    [ 'merge drops duplicates',
      <<<'PAD'
      {sequence '1..6', push='a'/}
      {sequence '4..9', push='b'/}
      {pull:a merge='b'}
        {$sequence},
      {/pull:a}
      PAD,
      '1,2,3,4,5,6,7,8,9,' ],

    [ 'combine keeps duplicates',
      <<<'PAD'
      {sequence '1..6', push='a'/}
      {sequence '4..9', push='b'/}
      {pull:a combine='b'}
        {$sequence},
      {/pull:a}
      PAD,
      '1,2,3,4,4,5,5,6,6,7,8,9,' ],

    [ 'intersection',
      <<<'PAD'
      {sequence '1..6', push='a'/}
      {sequence '4..9', push='b'/}
      {pull:a intersection='b'}
        {$sequence},
      {/pull:a}
      PAD,
      '4,5,6,' ],

    [ 'difference is symmetric',
      <<<'PAD'
      {sequence '1..6', push='a'/}
      {sequence '4..9', push='b'/}
      {pull:a difference='b'}
        {$sequence},
      {/pull:a}
      PAD,
      '1,2,3,7,8,9,' ],

    [ 'onlyNow is one-sided',
      <<<'PAD'
      {sequence '1..6', push='a'/}
      {sequence '4..9', push='b'/}
      {pull:a onlyNow='b'}
        {$sequence},
      {/pull:a}
      PAD,
      '1,2,3,' ],

    [ 'onlyStore is the other side',
      <<<'PAD'
      {sequence '1..6', push='a'/}
      {sequence '4..9', push='b'/}
      {pull:a onlyStore='b'}
        {$sequence},
      {/pull:a}
      PAD,
      '7,8,9,' ],

    [ 'difference with no store leaves it alone',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums difference}
        {$sequence},
      {/pull:nums}
      PAD,
      '1,2,3,4,5,6,7,8,9,10,' ],

    [ 'a store that does not exist leaves it alone',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums onlyNow='nosuch'}
        {$sequence},
      {/pull:nums}
      PAD,
      '1,2,3,4,5,6,7,8,9,10,' ],

    [ 'sort with a store sorts the union',
      <<<'PAD'
      {sequence '1..6', push='a'/}
      {sequence '4..9', push='b'/}
      {pull:a sort='b'}
        {$sequence},
      {/pull:a}
      PAD,
      '1,2,3,4,4,5,5,6,6,7,8,9,' ],


    [ 'shift shows what it removed',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums shift=2}
        {$sequence},
      {/pull:nums}
      PAD,
      '1,2,' ],

    [ 'shift leaves the rest in the store',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums shift=2/}
      {pull:nums}
        {$sequence},
      {/pull:nums}
      PAD,
      '3,4,5,6,7,8,9,10,' ],

    [ 'pop shows what it removed',
      <<<'PAD'
      {sequence '1..10', push='nums'/}
      {pull:nums pop=2}
        {$sequence},
      {/pull:nums}
      PAD,
      '9,10,' ],


    [ 'sum of an empty sequence is 0',
      <<<'PAD'
      {sequence '1..3', push='e'/}
      {pull:e onlyNow='e', sum}
        {$sequence},
      {/pull:e}
      PAD,
      '0,' ],

    [ 'count of an empty sequence is 0',
      <<<'PAD'
      {sequence '1..3', push='e'/}
      {pull:e onlyNow='e', count}
        {$sequence},
      {/pull:e}
      PAD,
      '0,' ],

    [ 'average of an empty sequence is empty',
      <<<'PAD'
      {sequence '1..3', push='e'/}
      {pull:e onlyNow='e', average}
        {$sequence},
      {/pull:e}
      PAD,
      '' ],

    [ 'median of an empty sequence is empty',
      <<<'PAD'
      {sequence '1..3', push='e'/}
      {pull:e onlyNow='e', median}
        {$sequence},
      {/pull:e}
      PAD,
      '' ],

    [ 'minimum answers in the original order, not sorted',
      <<<'PAD'
      {sequence '9;1;5;3', push='mmix'/}
      {pull:mmix minimum=3}
        {$sequence},
      {/pull:mmix}
      PAD,
      '1,5,3,' ],

    [ 'maximum answers in the original order, not sorted',
      <<<'PAD'
      {sequence '9;1;5;3', push='mmax'/}
      {pull:mmax maximum=3}
        {$sequence},
      {/pull:mmax}
      PAD,
      '9,5,3,' ],

    [ 'product of an empty sequence is 1',
      <<<'PAD'
      {sequence '1..3', push='e'/}
      {pull:e onlyNow='e', product}
        {$sequence},
      {/pull:e}
      PAD,
      '1,' ],

    [ 'distinct of an empty sequence is 0',
      <<<'PAD'
      {sequence '1..3', push='e'/}
      {pull:e onlyNow='e', distinct}
        {$sequence},
      {/pull:e}
      PAD,
      '0,' ],

  ];

?>