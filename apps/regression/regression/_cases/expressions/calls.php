<?php

  // The references that take arguments - one file each in eval/parms/, reached through
  // eval/type/parms.php, which is what collects the tokens after the name into $parm.
  //
  // Four kinds of callable answer to the same shape, and which one is chosen is decided by
  // padTypeFunction(): 'app' for a function the application supplies in _functions/, 'pad' for a
  // built-in, 'tag' for an ordinary PAD tag invoked as though it were a function, and 'php' for
  // a plain PHP function behind the php: prefix. The function: prefix is the one that says only
  // "read this name as a function" and leaves the kind to be resolved after, so it is checked
  // over both an app function and a built-in.
  //
  // action: and sequence: reach the sequence subsystem. Its expression entry wants its values as
  // the first argument - a literal array, or the name of a stored sequence - and folds the rest
  // into the action's parameter.

  return [

    [ 'php: calls a plain php function',
      <<<'PAD'
      {echo php:strtoupper('ab')}
      PAD,
      'AB' ],

    [ 'php: takes the piped value when nothing is written',
      <<<'PAD'
      {echo 'ab' | php:strtoupper}
      PAD,
      'AB' ],

    [ 'a built-in function name',
      <<<'PAD'
      {echo 'ab' | upper . '!'}
      PAD,
      'AB!' ],

    [ 'a function the application supplies',
      <<<'PAD'
      {echo 'ab' | exclaim}
      PAD,
      'ab!' ],

    [ 'function: over a built-in',
      <<<'PAD'
      {echo 'ab' | function:upper . '!'}
      PAD,
      'AB!' ],

    [ 'function: over one the application supplies',
      <<<'PAD'
      {echo 'ab' | function:exclaim}
      PAD,
      'ab!' ],

    // A tag invoked as a function is rebuilt as {name}value{/name} and run back through the
    // engine. Nothing carries a parameter across, so box falls back to the default it declares.

    [ 'an ordinary tag invoked as a function',
      <<<'PAD'
      {echo 'x' | box}
      PAD,
      '[none]' ],

    [ 'action: over a literal array',
      <<<'PAD'
      {echo '' | action:sum([1,2,3]) . ''}
      PAD,
      '6' ],

    [ 'action: counting instead of adding',
      <<<'PAD'
      {echo '' | action:count([3,1,2]) . ''}
      PAD,
      '3' ],

    // With no arguments at all there is nothing for the action to work on, and it answers empty
    // rather than failing - the same as the name without the prefix does.

    [ 'action: with nothing to act on',
      <<<'PAD'
      {echo 'z' | action:reverse}
      PAD,
      '' ],

    [ 'sequence: resolves an action of that name',
      <<<'PAD'
      {echo '' | sequence:sum([1,2,3]) . ''}
      PAD,
      '6' ],

  ];

?>
