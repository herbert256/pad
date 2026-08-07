<?php

  // The tags that stand for a value rather than doing work: the three fixed outcomes a template
  // can test against, the two that print a brace, and the small lookups.
  //
  // {true}, {false} and {null} are not the same miss. {false} takes the else branch, {null} puts
  // the level in its null state - which is what a null= option fires on and an else= one does
  // not - and level/flags.php tells them apart.

  return [

    [ 'true renders its content',
      <<<'PAD'
      {true}
        yes
      {/true}
      PAD,
      'yes' ],

    [ 'false does not',
      <<<'PAD'
      {false}
        yes
      {/false}
      no
      PAD,
      'no' ],

    [ 'null does not either',
      <<<'PAD'
      {null}
        yes
      {/null}
      no
      PAD,
      'no' ],

    // A brace cannot be written in a template, the parser reading it as the start of a tag, so
    // these two tags stand in for the characters. They hand back &open; and &close;, which are
    // turned into braces once the whole request is written.

    [ 'open and close print the braces',
      <<<'PAD'
      {open}a{close}
      PAD,
      '{a}' ],

    [ 'count is a test for anything at all',
      <<<'PAD'
      {data 'x'}
        ["b","a"]
      {/data}
      {count 'x'}
        some
      {/count}
      PAD,
      'some' ],

    [ 'and answers no for an empty set',
      <<<'PAD'
      {data 'x'}
        []
      {/data}
      {count 'x'}
        some
      {/count}
      no
      PAD,
      'no' ],

    [ 'exists is a plain file test',
      <<<'PAD'
      {exists '/no/such/file'}
        here
      {/exists}
      no
      PAD,
      'no' ],

    [ 'at looks a value up through the at subsystem',
      <<<'PAD'
      {data 'x'}
        [3,1,2]
      {/data}
      {at 'x'}
      PAD,
      '3' ],

    [ 'foo is the built-in the prefix pages compare against',
      <<<'PAD'
      {pad:foo}
      PAD,
      'Foo tag from pad' ],

  ];

?>