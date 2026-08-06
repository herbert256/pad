<?php

  // The property@tag values an iteration publishes.
  //
  // A property is written as a tag pair - {first@xs}...{/first@xs} - not inside a condition:
  // {if first@xs} ends the request, because a property resolves to an array and comparing one
  // against a value is not implemented. CLAUDE.md used to show the condition form.
  //
  // The separators are covered from both sides, notFirst@ and notLast@, and border@ as the pair of
  // them; a change that made one of the three agree with the wrong rows would otherwise show up in
  // only one case.
  //
  // The singular forms - option.name@, variable.name@, parameter.n@ - take their argument after a
  // dot and each had no forwarder in at/properties/, so none of them could be reached at all.

  return [

    [ 'first@ wraps the opening',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {first@xs}[{/first@xs}
        {$xs},
      {/xs}
      PAD,
      '[7,8,' ],

    [ 'last@ wraps the closing',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {$xs},
        {last@xs}]{/last@xs}
      {/xs}
      PAD,
      '7,8,]' ],

    [ 'notFirst@ makes a separator',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {notFirst@xs}-{/notFirst@xs}
        {$xs}
      {/xs}
      PAD,
      '7-8' ],

    [ 'notLast@ is the other separator',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {$xs}
        {notLast@xs},{/notLast@xs}
      {/xs}
      PAD,
      '7,8' ],

    [ 'border@ is the first row or the last',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {border@xs}|{/border@xs}
        {$xs}
      {/xs}
      PAD,
      '|7|8' ],

    [ 'middle@ is neither end',
      <<<'PAD'
      {data 'ys'}
        [1,2,3]
      {/data}
      {ys}
        {middle@ys}M{/middle@ys}
      {/ys}
      PAD,
      'M' ],

    [ 'even@ hits the even rows',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {even@xs}E{/even@xs}
      {/xs}
      PAD,
      'E' ],

    [ 'odd@ hits the rest',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {odd@xs}O{/odd@xs}
      {/xs}
      PAD,
      'O' ],

    [ 'current@ counts from one',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {current@xs},
      {/xs}
      PAD,
      '1,2,' ],

    [ 'count@ is the same on every row',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {count@xs},
      {/xs}
      PAD,
      '2,2,' ],

    [ 'name@ is the tag',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {name@xs},
      {/xs}
      PAD,
      'xs,xs,' ],

    [ 'even@ as a ternary',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {even@xs ? even : odd},
      {/xs}
      PAD,
      '1,,' ],

    [ 'key@ counts from zero',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {key@xs},
      {/xs}
      PAD,
      '0,1,' ],

    [ 'remaining@ counts down to zero',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {remaining@xs},
      {/xs}
      PAD,
      '1,0,' ],

    [ 'done@ counts the rows already finished',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {done@xs},
      {/xs}
      PAD,
      '0,1,' ],

    [ 'fields@ walks the fields of a row',
      <<<'PAD'
      {data 'u'}
        [{"a":1,"b":2}]
      {/data}
      {u}
        {fields@u}
          {$name}={$value},
        {/fields@u}
      {/u}
      PAD,
      'a=1,b=2,' ],

    [ 'firstFieldName@ names the first field',
      <<<'PAD'
      {data 'u'}
        [{"a":1,"b":2}]
      {/data}
      {u}
        {firstFieldName@u},
      {/u}
      PAD,
      'a,' ],

    [ 'firstFieldValue@ gives its value',
      <<<'PAD'
      {data 'u'}
        [{"a":1,"b":2}]
      {/data}
      {u}
        {firstFieldValue@u},
      {/u}
      PAD,
      '1,' ],

    [ 'option.name@ reads one option',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs mask='Z'}
        {option.mask@xs},
      {/xs}
      PAD,
      'Z,Z,' ],

    [ 'options@ walks them all',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs mask='Z'}
        {options@xs}
          {$name}={$value},
        {/options@xs}
      {/xs}
      PAD,
      'mask=Z,mask=Z,' ],

    [ 'variable.name@ reads one level variable',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs $v = 5}
        {variable.v@xs},
      {/xs}
      PAD,
      '5,5,' ],

    [ 'variables@ walks them all',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs $v = 5}
        {variables@xs}
          {$name}={$value},
        {/variables@xs}
      {/xs}
      PAD,
      'v=5,v=5,' ],

    [ 'parameter.n@ reads one parameter',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs 'a', 'b'}
        {parameter.1@xs},
      {/xs}
      PAD,
      'a,a,' ],

    [ 'parameters@ walks them all',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs 'a', 'b'}
        {parameters@xs}
          {$value},
        {/parameters@xs}
      {/xs}
      PAD,
      'a,b,a,b,' ],

  ];

?>
