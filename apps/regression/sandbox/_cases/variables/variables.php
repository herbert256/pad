<?php

  // Assignment and scope.
  //
  // A level variable declared on a tag holds the same value for every row; an occurrence variable,
  // declared with %, is worked out again for each one. Both are read back as ordinary fields with
  // $ - the % is the assignment sigil only.
  //
  // A variable set inside a loop is still there after it: the loop does not get a scope of its
  // own, which is what makes a running total work.

  return [

    [ 'set and read',
      <<<'PAD'
      {set $n = 'Alice'/}
      {$n}
      PAD,
      'Alice' ],

    [ 'set a number',
      <<<'PAD'
      {set $n = 42/}
      {$n}
      PAD,
      '42' ],

    [ 'set an expression',
      <<<'PAD'
      {set $t = 3 * 4/}
      {$t}
      PAD,
      '12' ],

    [ 'set two at once',
      <<<'PAD'
      {set $a = 1, $b = 2/}
      {$a}{$b}
      PAD,
      '12' ],

    [ 'set from a concatenation',
      <<<'PAD'
      {set $a = 'x' . 'y'/}
      {$a}
      PAD,
      'xy' ],

    [ 'one variable from another',
      <<<'PAD'
      {set $a = 2/}
      {set $b = $a + 3/}
      {$b}
      PAD,
      '5' ],

    [ 'set is visible inside a loop',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {set $p = '#'/}
      {xs}
        {$p}{$xs},
      {/xs}
      PAD,
      '#7,#8,' ],

    [ 'a level variable holds for every row',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs $lvl = 7}
        {$lvl},
      {/xs}
      PAD,
      '7,7,' ],

    [ 'an occurrence variable changes per row',
      <<<'PAD'
      {data 'st'}
        [{"s":10,"b":2},{"s":20,"b":3}]
      {/data}
      {st %t = $s + $b}
        {$t},
      {/st}
      PAD,
      '12,23,' ],

    [ 'an occurrence variable over an expression',
      <<<'PAD'
      {data 'st'}
        [{"s":10,"b":2},{"s":20,"b":3}]
      {/data}
      {st %t = $s * 2}
        {$t},
      {/st}
      PAD,
      '20,40,' ],

    [ 'a level variable beside an occurrence one',
      <<<'PAD'
      {data 'st'}
        [{"s":10,"b":2},{"s":20,"b":3}]
      {/data}
      {st $lvl = 9, %t = $s}
        {$lvl}:{$t},
      {/st}
      PAD,
      '9:10,9:20,' ],

    [ 'a field of the row',
      <<<'PAD'
      {data 'u'}
        [{"n":"bob"},{"n":"amy"}]
      {/data}
      {u}
        {$n},
      {/u}
      PAD,
      'bob,amy,' ],

    [ 'a variable set inside a loop outlives the row',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {set $seen = $xs/}
      {/xs}
      {$seen}
      PAD,
      '8' ],

    [ 'a running total over a loop',
      <<<'PAD'
      {set $t = 0/}
      {set $i = 1/}
      {while $i le 4}
        {set $t = $t + $i/}
        {increment $i/}
      {/while}
      {$t}
      PAD,
      '10' ],

    [ 'a pipe applies to a variable',
      <<<'PAD'
      {set $a = ' q '/}
      {echo $a | trim | upper}
      PAD,
      'Q' ],

    [ 'property: reads an iteration value',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {property:current},
      {/xs}
      PAD,
      '1,2,' ],

    [ 'parm: reads a tag parameter',
      <<<'PAD'
      {withparm label='hi'/}
      PAD,
      'hi' ],

    [ 'a level variable is evaluated once, not per row',
      <<<'PAD'
      {data 'st'}
        [1,2]
      {/data}
      {st $lvl = $caseLvlSrc}
        {$lvl},
        {set $caseLvlSrc = 9/}
      {/st}
      PAD,
      '5,5,',
      [ 'caseLvlSrc' => 5 ] ],

  ];

?>