<?php

  // The functions that take a value apart: by delimiter, and by position.
  //
  // after and afterLast used to disagree about the delimiter: after dropped it and afterLast
  // left it on the front of the answer, which neither FUNCTIONS.md nor after itself agreed
  // with. afterLast skips the whole delimiter now, so a multi-character one is dropped whole,
  // and a value that does not contain it comes back untouched rather than a character short.
  //
  // Note also that the position functions do not share a base: substr counts from zero, mid from
  // one. Each is checked past the end of the value as well, where a wrong bound would show.

  return [

    [ 'after takes what follows the delimiter',
      <<<'PAD'
      {echo 'hello/world/test' | after('/')}
      PAD,
      'world/test' ],

    [ 'after with no delimiter drops one character',
      <<<'PAD'
      {echo 'abc' | after('/')}
      PAD,
      'bc' ],

    [ 'afterLast drops the delimiter, as after does',
      <<<'PAD'
      {echo 'hello/world/test' | afterLast('/')}
      PAD,
      'test' ],

    [ 'afterLast drops a multi-character delimiter whole',
      <<<'PAD'
      {echo 'aXXbXXc' | afterLast('XX')}
      PAD,
      'c' ],

    [ 'afterLast with no delimiter gives the value back',
      <<<'PAD'
      {echo 'abc' | afterLast('/')}
      PAD,
      'abc' ],

    [ 'before takes what precedes it',
      <<<'PAD'
      {echo 'hello/world/test' | before('/')}
      PAD,
      'hello' ],

    [ 'before with no delimiter gives nothing',
      <<<'PAD'
      {echo 'abc' | before('/')}
      PAD,
      '' ],

    [ 'beforeLast',
      <<<'PAD'
      {echo 'hello/world/test' | beforeLast('/')}
      PAD,
      'hello/world' ],

    [ 'beforeLast on a dotted name',
      <<<'PAD'
      {echo 'a.b.c' | beforeLast('.')}
      PAD,
      'a.b' ],

    [ 'left',
      <<<'PAD'
      {echo 'Hello World' | left(5)}
      PAD,
      'Hello' ],

    [ 'left past the end gives all of it',
      <<<'PAD'
      {echo 'ab' | left(5)}
      PAD,
      'ab' ],

    [ 'right',
      <<<'PAD'
      {echo 'Hello World' | right(5)}
      PAD,
      'World' ],

    [ 'right past the end gives all of it',
      <<<'PAD'
      {echo 'ab' | right(5)}
      PAD,
      'ab' ],

    [ 'mid counts from one',
      <<<'PAD'
      {echo 'abcdef' | mid(2, 3)}
      PAD,
      'bcd' ],

    [ 'substr counts from zero',
      <<<'PAD'
      {echo 'Hello World' | substr(6)}
      PAD,
      'World' ],

    [ 'substr with a length',
      <<<'PAD'
      {echo 'Hello World' | substr(0, 5)}
      PAD,
      'Hello' ],

    [ 'substr from the right',
      <<<'PAD'
      {echo 'abcdef' | substr(-2)}
      PAD,
      'ef' ],

    [ 'max_len truncates',
      <<<'PAD'
      {echo 'Hello World' | max_len(5)}
      PAD,
      'Hello' ],

    [ 'max_len leaves a short value',
      <<<'PAD'
      {echo 'Hi' | max_len(5)}
      PAD,
      'Hi' ],

    [ 'max_len of nothing',
      <<<'PAD'
      {echo 'abc' | max_len(0)}
      PAD,
      '' ],

    [ 'extraction feeding a chain',
      <<<'PAD'
      {echo 'hello world' | left(5) | upper}
      PAD,
      'HELLO' ],

  ];

?>