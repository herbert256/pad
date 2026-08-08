<?php

  // A {code} pass inside another nested pass - which is what every case here is, since a
  // sandbox case is itself a nested pass.
  //
  // The four option forms differ on what a pass newly creates: a plain {code} and reset
  // leave the created variable standing, clean and sandbox unset it on the way out -
  // start/pad/end.php runs the unset for those two only.

  return [

    [ 'a nested pass with reset survives inside a nested pass',
      <<<'PAD'
      A{code reset}{set $nf1 = 'x'/}{/code}B
      PAD,
      'AB' ],

    [ 'the same with clean',
      <<<'PAD'
      A{code clean}{set $nf2 = 'x'/}{/code}B
      PAD,
      'AB' ],

    [ 'the same with sandbox',
      <<<'PAD'
      A{code sandbox}{set $nf3 = 'x'/}{/code}B
      PAD,
      'AB' ],

    [ 'a plain pass leaks what it created',
      <<<'PAD'
      {code}{set $nzN = 'q'/}{/code}[{$nzN | optional}]
      PAD,
      '[q]' ],

    [ 'reset leaks it too - reset restores, it does not unset',
      <<<'PAD'
      {code reset}{set $nzR = 'q'/}{/code}[{$nzR | optional}]
      PAD,
      '[q]' ],

    [ 'clean unsets what the pass created',
      <<<'PAD'
      {code clean}{set $nzC = 'q'/}{/code}[{$nzC | optional}]
      PAD,
      '[]' ],

    [ 'sandbox unsets it as well',
      <<<'PAD'
      {code sandbox}{set $nzS = 'q'/}{/code}[{$nzS | optional}]
      PAD,
      '[]' ],

  ];

?>
