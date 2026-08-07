<?php

  // The constructs written inside a tag's content rather than on the tag: @start@, @end@ and
  // @else@, and what happens where an occurrence is walked one at a time instead of collected
  // up front.
  //
  // @start@ and @end@ narrow what repeats. Everything before @start@ is emitted once before the
  // first occurrence and everything after @end@ once after the last, so a table can carry its
  // header and footer inside the same tag as its rows.
  //
  // Walking is the manual's word for a tag that produces its occurrences one at a time - {while}
  // and {until} - rather than collecting them first. It is what breaks the properties: with
  // nothing collected there is no set to be first or last of, so every row answers to both. The
  // manual gives the way round it, which is the last case: put the walk in the data store with
  // toData= and iterate the store, which is a collected set like any other.

  return [

    [ 'start and end narrow what repeats',
      <<<'PAD'
      {data 'n'}
        [1,2,3]
      {/data}
      {n}
        (@start@{$n}@end@)
      {/n}
      PAD,
      '(123)' ],

    [ 'with nothing to repeat the else branch runs instead',
      <<<'PAD'
      {data 'n'}
        []
      {/data}
      {n}
        (@start@{$n}@end@)
      @else@
        none
      {/n}
      PAD,
      'none' ],

    [ 'a collected tag knows which occurrence is which',
      <<<'PAD'
      {sequence '1..4'}
        {first}[{/first}
        {$sequence}
        {last}]{/last}
      {/sequence}
      PAD,
      '[1234]' ],

    // The same template over a walking tag. There is no collected set to be last of, so every row
    // answers to {last} and the closing bracket comes out four times instead of once. This is the
    // documented limitation, not a defect - the case is here so that a change to it is noticed.

    [ 'a walking tag makes every row its own last',
      <<<'PAD'
      {set $i = 1/}
      {while $i le 4}
        {first}[{/first}
        {$i}
        {last}]{/last}
        {increment $i/}
      {/while}
      PAD,
      '[1]2]3]4]' ],

    [ 'storing the walk and iterating the store puts them back',
      <<<'PAD'
      {set $i = 1/}
      {while $i le 3, toData='w'}
        {increment $i/}
      {/while}
      {data:w}
        {first}[{/first}
        x
        {last}]{/last}
      {/data:w}
      PAD,
      '[xxx]' ],

    [ 'until walks the other way',
      <<<'PAD'
      {set $i = 0/}
      {until $i eq 3}
        {increment $i/}
        {$i},
      {/until}
      PAD,
      '1,2,3,' ],

  ];

?>
