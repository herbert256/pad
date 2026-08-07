<?php

  // Where the operands of a pipe segment come from when the template does not write both.
  //
  // One file in eval/actions/ per shape, each substituting $myself - the value coming down the
  // pipe - for whatever is missing:
  //
  //   double.php       both sides written        {echo '' | 2 + 3}
  //   doubleLeft.php   nothing on the left       {echo 5 | + 1}
  //   doubleRight.php  nothing on the right      {echo 5 | 100 -}
  //   alone.php        nothing either side       {echo 5 | *}
  //
  // The last is the one worth remembering: an operator on its own uses the piped value twice, so
  // a segment of just * squares it.
  //
  // eval/fast.php is here too, the path an expression takes when it is nothing but the name of a
  // built-in function: parsing is skipped entirely and the function file is included directly.

  return [

    [ 'both operands written',
      <<<'PAD'
      {echo '' | 2 + 3}
      PAD,
      '5' ],

    [ 'no left operand takes the piped value',
      <<<'PAD'
      {echo 5 | + 1}
      PAD,
      '6' ],

    [ 'no right operand takes the piped value',
      <<<'PAD'
      {echo 5 | 100 -}
      PAD,
      '95' ],

    [ 'an operator alone uses the piped value twice',
      <<<'PAD'
      {echo 5 | *}
      PAD,
      '25' ],

    [ 'alone with minus, which cancels',
      <<<'PAD'
      {echo 4 | -}
      PAD,
      '0' ],

    [ 'alone with concatenation, which doubles',
      <<<'PAD'
      {echo 3 | .}
      PAD,
      '33' ],

    [ 'a bare function name skips parsing altogether',
      <<<'PAD'
      {echo 'ab' | upper}
      PAD,
      'AB' ],

    [ 'segments chain, each taking the last result',
      <<<'PAD'
      {echo 5 | + 1 | * 2 | 100 -}
      PAD,
      '88' ],

  ];

?>
