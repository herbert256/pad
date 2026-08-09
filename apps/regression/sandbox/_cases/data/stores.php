<?php

  // Where the name of a data or content block is written, and what that changes.
  //
  // The manual says it plainly and neither half had a case: a name on the *opening* tag stores
  // the block unprocessed, and a name on the *closing* tag stores it processed. So the first form
  // is resolved wherever it is used and the second wherever it was written - which is the whole
  // difference, and it shows the moment a variable inside the block changes in between.
  //
  // The two formats the data group did not have are here too: YAML, and PAD's own list, which is
  // a plain comma list between round brackets.

  return [

    [ 'a name on the opening tag stores the content unprocessed',
      <<<'PAD'
      {set $v = 'X'/}
      {content 'c'}[{$v}]{/content}
      {set $v = 'Y'/}
      {content:c}
      PAD,
      '[Y]' ],

    [ 'a name on the closing tag stores it processed',
      <<<'PAD'
      {set $v = 'X'/}
      {content}[{$v}]{/content 'c'}
      {set $v = 'Y'/}
      {content:c}
      PAD,
      '[X]' ],

    [ 'the same for data, named on the closing tag',
      <<<'PAD'
      {set $n = 3/}
      {data}[1,{$n}]{/data 'd'}
      {d}
        {$d},
      {/d}
      PAD,
      '1,3,' ],

    // The entries of a PAD list are evaluated, not taken as text, so a bare word that happens to
    // name something resolves to it - written unquoted this case read 'a,5,green,' once other
    // groups had defined b and c. Quoting each entry is what makes it a list of those strings.

    [ "PAD's own list format, between round brackets",
      <<<'PAD'
      {data 'd'}
        ('a','b','c')
      {/data}
      {d}
        {$d},
      {/d}
      PAD,
      'a,b,c,' ],

    [ 'yaml, named by the type option',
      <<<'PAD'
      {data 'd', type='yaml'}
      - 1
      - 2
      {/data}
      {d}
        {$d},
      {/d}
      PAD,
      '1,2,' ],

    [ 'a range is recognised on sight',
      <<<'PAD'
      {data 'dr'}2..6{/data}
      {dr}
        {$dr},
      {/dr}
      PAD,
      '2,3,4,5,6,' ],

    [ 'a letter range, named by the type option',
      <<<'PAD'
      {data 'dl', type='range'}a..e{/data}
      {dl}
        {$dl},
      {/dl}
      PAD,
      'a,b,c,d,e,' ],

  ];

?>