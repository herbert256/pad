<?php

  // The data tag over the formats it accepts.
  //
  // JSON, CSV and XML each reach the same shape - a list of rows the tag iterates - so each is
  // checked through the same kind of template, and the CSV and XML cases assert the field names
  // they derive rather than only the values.
  //
  // The last cases are about the store rather than the format: that reading it twice gives the
  // same answer both times, and that a store defined inside a loop is reachable per row.

  return [

    [ 'a json list',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs}
        {$xs},
      {/xs}
      PAD,
      '1,2,3,' ],

    [ 'a json list of objects',
      <<<'PAD'
      {data 'u'}
        [{"n":"bob"},{"n":"amy"}]
      {/data}
      {u}
        {$n},
      {/u}
      PAD,
      'bob,amy,' ],

    [ 'a nested object still gives its own fields',
      <<<'PAD'
      {data 'u'}
        [{"n":"bob","t":{"x":9}}]
      {/data}
      {u}
        {$n},
      {/u}
      PAD,
      'bob,' ],

    [ 'csv, the first line names the fields',
      <<<'PAD'
      {data 'p'}
        name,phone
        bob,123
        amy,456
      {/data}
      {p}
        {$name}={$phone},
      {/p}
      PAD,
      'bob=123,amy=456,' ],

    [ 'csv with three columns',
      <<<'PAD'
      {data 'p'}
        a,b,c
        1,2,3
      {/data}
      {p}
        {$a}{$b}{$c}
      {/p}
      PAD,
      '123' ],

    [ 'xml rows and attributes',
      <<<'PAD'
      {data 'p'}
        <data><row name="bob" /><row name="amy" /></data>
      {/data}
      {p}
        {$name},
      {/p}
      PAD,
      'bob,amy,' ],

    [ 'xml with two attributes each',
      <<<'PAD'
      {data 'p'}
        <data><row a="1" b="2"/><row a="3" b="4"/></data>
      {/data}
      {p}
        {$a}{$b},
      {/p}
      PAD,
      '12,34,' ],

    [ 'an empty list renders nothing',
      <<<'PAD'
      {data 'e'}
        []
      {/data}
      {e}
        x
      {/e}
      PAD,
      '' ],

    [ 'count tests for elements',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {count 'xs'}
        has
      {/count}
      PAD,
      'has' ],

    [ 'count on an empty list is false',
      <<<'PAD'
      {data 'e'}
        []
      {/data}
      {count 'e'}
        has
      {/count}
      PAD,
      '' ],

    [ 'a store can be read twice',
      <<<'PAD'
      {data 'xs'}
        [1,2]
      {/data}
      {xs}
        {$xs},
      {/xs}
      {xs}
        {$xs},
      {/xs}
      PAD,
      '1,2,1,2,' ],

    [ 'a store defined inside a loop',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs}
        {data 'ys'}
          [7,8]
        {/data}
        {ys}
          {$xs}{$ys},
        {/ys}
      {/xs}
      PAD,
      '17,18,27,28,37,38,' ],

    [ 'a condition inside a loop',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs}
        {if $xs gt 1}
          {$xs},
        {/if}
      {/xs}
      PAD,
      '2,3,' ],

  ];

?>