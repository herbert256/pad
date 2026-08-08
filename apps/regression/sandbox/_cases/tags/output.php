<?php

  // Output: what a tag puts into the page, and the tags that carry a value rather than a branch.
  //
  // echo evaluates and prints, a bare {$name} prints a variable, set assigns, increment and
  // decrement move a counter, and ignore hands back the braces it was given instead of parsing
  // them. true, false and null are the three fixed answers, and are here because a tag that always
  // renders and one that never does are what every conditional is measured against.
  //
  // The lone zero is here because it is the value PHP most likes to read as false: a tag
  // whose whole output is the single character 0 must still print it, and this is where a
  // truthiness test in the engine's own plumbing would show.
  //
  // A name nothing claims is put back into the page exactly as it was written, which is what
  // level/no.php says it does. That must not depend on position - a first unclaimed tag and
  // a later one answer alike - which holds because inits/vars.php declares $padBetweenOrg
  // at boot, so a nested pass reads and writes the same global as the request around it.

  return [

    [ 'echo a literal',
      <<<'PAD'
      {echo 'hi'}
      PAD,
      'hi' ],

    [ 'echo an expression',
      <<<'PAD'
      {echo 2 * 3}
      PAD,
      '6' ],

    [ 'echo a variable',
      <<<'PAD'
      {set $n = 'Alice'/}
      {echo $n}
      PAD,
      'Alice' ],

    [ 'a variable prints by name',
      <<<'PAD'
      {set $n = 'Alice'/}
      {$n}
      PAD,
      'Alice' ],

    [ 'set an expression',
      <<<'PAD'
      {set $t = 3 * 4/}
      {$t}
      PAD,
      '12' ],

    [ 'set from another variable',
      <<<'PAD'
      {set $a = 2/}
      {set $b = $a + 3/}
      {$b}
      PAD,
      '5' ],

    [ 'set two in one tag',
      <<<'PAD'
      {set $a = 1, $b = 2/}
      {$a}{$b}
      PAD,
      '12' ],

    [ 'increment',
      <<<'PAD'
      {set $i = 5/}
      {increment $i/}
      {$i}
      PAD,
      '6' ],

    [ 'decrement',
      <<<'PAD'
      {set $i = 5/}
      {decrement $i/}
      {$i}
      PAD,
      '4' ],

    [ 'ignore leaves braces alone',
      <<<'PAD'
      {ignore}
        {not a tag}
      {/ignore}
      PAD,
      '{not a tag}' ],

    [ 'switch alternates over two',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs}
        {switch 'a', 'b'},
      {/xs}
      PAD,
      'a,b,a,' ],

    [ 'switch alternates over three',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs}
        {switch 'a', 'b', 'c'},
      {/xs}
      PAD,
      'a,b,c,' ],

    [ 'true always renders',
      <<<'PAD'
      {true}
        y
      {/true}
      PAD,
      'y' ],

    [ 'false never renders',
      <<<'PAD'
      {false}
        y
      {/false}
      PAD,
      '' ],

    [ 'null renders nothing',
      <<<'PAD'
      {null}
        y
      {/null}
      PAD,
      '' ],

    [ 'exists finds a file',
      <<<'PAD'
      {exists 'index.php'}
        y
      {/exists}
      PAD,
      'y' ],

    [ 'exists on a missing file',
      <<<'PAD'
      {exists 'nope.xyz'}
        y
      {/exists}
      PAD,
      '' ],

    [ 'code runs its content and returns only the result',
      <<<'PAD'
      {code}
        {echo 'in'}
      {/code}
      PAD,
      'in' ],

    [ 'code over text and a tag together',
      <<<'PAD'
      {code}
        x{echo 'y'}
      {/code}
      PAD,
      'xy' ],

    [ 'sandbox does the same',
      <<<'PAD'
      {sandbox}
        {echo 'in'}
      {/sandbox}
      PAD,
      'in' ],

    [ 'an unknown tag is put back as it was written',
      <<<'PAD'
      {nosuchtag}
      PAD,
      '{nosuchtag}' ],

    [ 'a lone zero is printed like any other value',
      <<<'PAD'
      A{echo 0}B
      PAD,
      'A0B' ],

    [ 'a zero inside a longer value is printed',
      <<<'PAD'
      {echo '0 items'}
      PAD,
      '0 items' ],

  ];

?>