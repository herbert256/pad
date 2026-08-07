<?php

  // The callback= option, which hands a tag's data to a PHP file in _callbacks/ before or while
  // the level renders. The file is called three times - $padCallback is 'init', then 'row' once
  // for each row, then 'exit' - so one file serves the whole run. The fixtures are
  // _callbacks/before.php and _callbacks/demand.php.
  //
  // With the before option the whole set is put through the callback up front. Rows are passed by
  // reference in $row, so the callback can give a row a field the data never had, and anything it
  // works out across all of them is finished before the first occurrence renders - which is what
  // lets a total appear above the rows it totals rather than below.
  //
  // Without before the callback streams: init before the first row, row per occurrence, exit
  // after the last. The mark case spells that order out. What a streaming row phase reads is the
  // occurrence's own fields as plain PHP variables, which the manual's staff example shows and no
  // case here can - a case renders in a nested pass, where those fields are that pass's locals
  // rather than globals, so the callback cannot see them.

  return [

    [ 'a before callback can add a field to every row',
      <<<'PAD'
      {data 'n'}
        [1,2,3]
      {/data}
      {n callback='before.php', before}
        {$double},
      {/n}
      PAD,
      '2,4,6,' ],

    [ 'and finishes its total before the first occurrence',
      <<<'PAD'
      {data 'n'}
        [1,2,3]
      {/data}
      {n callback='before.php', before}
        {first}{$sum}:{/first}
        {$n},
      {/n}
      PAD,
      '[6]:1,2,3,' ],

    [ 'without before it runs init, then a row each, then exit',
      <<<'PAD'
      {data 'n'}
        [1,2,3]
      {/data}
      {n callback='demand.php'}
        {$n},
      {/n}
      {$mark}
      PAD,
      '1,2,3,irrrx' ],

  ];

?>