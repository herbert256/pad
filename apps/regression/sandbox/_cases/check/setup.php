<?php

  // Proving the setup entry, before the check pages that need it are ported onto it.
  //
  // These are the shapes the check application's paired .php files use: a scalar, a nested
  // array, and a list of rows. Each is stated in the case rather than in a file beside it.
  //
  // That the setup is dropped again cannot be asserted directly - reading a variable that is
  // not set raises a PAD error and would end the request rather than answer - so the last case
  // settles for the half of it that can be: a name reused by a later case carries that case's
  // value, not the earlier one's.

  return [

    [ 'a scalar put in place before the template runs',
      <<<'PAD'
      {$abc}
      PAD,
      '123',
      [ 'abc' => '123' ] ],

    [ 'a nested array, read with dots',
      <<<'PAD'
      {$deep.1.1.xyz}
      PAD,
      '789',
      [ 'deep' => [ 1 => [ 1 => [ 'xyz' => '789' ] ] ] ] ],

    [ 'a list of rows, iterated as data',
      <<<'PAD'
      {rows}
        {$name},
      {/rows}
      PAD,
      'ann,bob,',
      [ 'rows' => [ [ 'name' => 'ann' ], [ 'name' => 'bob' ] ] ] ],

    [ 'two variables at once',
      <<<'PAD'
      {$one}{$two}
      PAD,
      'AB',
      [ 'one' => 'A', 'two' => 'B' ] ],

    [ 'a second case does not see the first one\'s value',
      <<<'PAD'
      {$one}
      PAD,
      'Z',
      [ 'one' => 'Z' ] ],

  ];

?>