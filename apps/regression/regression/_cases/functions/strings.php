<?php

  // The functions that rewrite a value without changing what kind of thing it is.
  //
  // Each is checked on a value where a wrong answer cannot pass for a right one - a replace that
  // matches nothing must give the value back rather than an empty string, a trim must leave the
  // spacing inside the value alone, and a chain must apply left to right.

  return [

    [ 'upper',
      <<<'PAD'
      {echo 'hello world' | upper}
      PAD,
      'HELLO WORLD' ],

    [ 'lower',
      <<<'PAD'
      {echo 'HELLO WORLD' | lower}
      PAD,
      'hello world' ],

    [ 'capitalize',
      <<<'PAD'
      {echo 'hello world' | capitalize}
      PAD,
      'Hello World' ],

    [ 'ucwords is the same function',
      <<<'PAD'
      {echo 'hello world' | ucwords}
      PAD,
      'Hello World' ],

    [ 'trim',
      <<<'PAD'
      {echo '  hello  ' | trim}
      PAD,
      'hello' ],

    [ 'trim leaves the inside alone',
      <<<'PAD'
      {echo '  a b  ' | trim}
      PAD,
      'a b' ],

    [ 'white collapses runs of space',
      <<<'PAD'
      {echo 'hello   world' | white}
      PAD,
      'hello world' ],

    [ 'white collapses a tab too',
      <<<'PAD'
      {echo 'a 	  b' | white}
      PAD,
      'a b' ],

    [ 'replace',
      <<<'PAD'
      {echo 'hello world' | replace('world', 'there')}
      PAD,
      'hello there' ],

    [ 'replace with no match leaves it alone',
      <<<'PAD'
      {echo 'abc' | replace('z', 'y')}
      PAD,
      'abc' ],

    [ 'cut removes every occurrence',
      <<<'PAD'
      {echo 'hello world' | cut('o')}
      PAD,
      'hell wrld' ],

    [ 'cut takes more than one character',
      <<<'PAD'
      {echo 'banana' | cut('an')}
      PAD,
      'ba' ],

    [ 'chained functions run left to right',
      <<<'PAD'
      {echo '  Hello  ' | trim | upper}
      PAD,
      'HELLO' ],

    [ 'a chain can undo itself',
      <<<'PAD'
      {echo 'MiXeD' | lower | upper}
      PAD,
      'MIXED' ],

    [ 'three in a chain',
      <<<'PAD'
      {echo '  hello world  ' | trim | capitalize | cut(' ')}
      PAD,
      'HelloWorld' ],

  ];

?>
