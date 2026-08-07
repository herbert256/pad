<?php

  // The print option and the four that format what it prints.
  //
  // print appends the firstFieldValue property to the content, so every occurrence prints its
  // first field without the template naming it, and then applies quote, open, glue and close in
  // that order - which is the only place those four handlers are reached from. Each wraps the
  // content in a property pair rather than emitting text directly: open in {first}, close in
  // {last}, glue in {notLast}, which is what puts them once at the ends and between the rest.

  return [

    [ 'print writes each occurrence',
      <<<'PAD'
      {data 'x'}
        ["b","a"]
      {/data}
      {x print}
      PAD,
      'ba' ],

    [ 'glue goes between, and not after the last',
      <<<'PAD'
      {data 'x'}
        ["b","a"]
      {/data}
      {x print, glue=', '}
      PAD,
      'b, a' ],

    [ 'quote wraps every occurrence',
      <<<'PAD'
      {data 'x'}
        ["b","a"]
      {/data}
      {x print, quote='"'}
      PAD,
      '"b""a"' ],

    [ 'open and close wrap the whole run once',
      <<<'PAD'
      {data 'x'}
        ["b","a"]
      {/data}
      {x print, open='(', close=')'}
      PAD,
      '(ba)' ],

    [ 'all four together, in the order print applies them',
      <<<'PAD'
      {data 'x'}
        ["b","a"]
      {/data}
      {x print, open='(', glue='|', close=')', quote='*'}
      PAD,
      '(*b*|*a*)' ],

    [ 'one occurrence gets no glue at all',
      <<<'PAD'
      {data 'x'}
        ["b"]
      {/data}
      {x print, glue=', '}
      PAD,
      'b' ],

  ];

?>