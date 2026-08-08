<?php

  // The functions that take a value apart: by delimiter, and by position.
  //
  // The four delimiter functions share one convention: the whole delimiter is skipped,
  // multi-character or not, and a value that does not contain it comes back untouched. The
  // absent-delimiter and multi-character cases below are what hold the convention - a
  // strpos that answers FALSE is one coercion away from eating a character or emptying the
  // value, and these are the inputs where that would show.
  //
  // Note also that the position functions do not share a base: substr counts from zero, mid from
  // one. Each is checked past the end of the value as well, where a wrong bound would show.
  //
  // The boundary cases at the end state the decided contracts: a zero or negative count
  // names no characters, a mid start below 1 is read as 1, and an empty delimiter is found
  // everywhere - at the start for the first-forms, at the end for the last-forms - so each
  // of the four answers follows from where it was found.

  return [

    [ 'after takes what follows the delimiter',
      <<<'PAD'
      {echo 'hello/world/test' | after('/')}
      PAD,
      'world/test' ],

    [ 'after with no delimiter gives the value back',
      <<<'PAD'
      {echo 'abc' | after('/')}
      PAD,
      'abc' ],

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

    [ 'after drops a multi-character delimiter whole',
      <<<'PAD'
      {echo 'a--b--c' | after('--')}
      PAD,
      'b--c' ],

    [ 'beforeLast with no delimiter gives the value back',
      <<<'PAD'
      {echo 'abc' | beforeLast('/')}
      PAD,
      'abc' ],

    [ 'before with no delimiter gives the value back',
      <<<'PAD'
      {echo 'abc' | before('/')}
      PAD,
      'abc'],

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

    [ 'mid past the end gives what is there',
      <<<'PAD'
      {echo 'abc' | mid(2, 10)}
      PAD,
      'bc' ],

    [ 'substr past the end gives nothing',
      <<<'PAD'
      {echo 'abc' | substr(20)}
      PAD,
      '' ],

    [ 'left of zero is nothing',
      <<<'PAD'
      {echo 'abc' | left(0)}
      PAD,
      '' ],

    [ 'right of zero is nothing too',
      <<<'PAD'
      {echo 'abc' | right(0)}
      PAD,
      '' ],

    [ 'a negative left count is nothing, not a trim',
      <<<'PAD'
      {echo 'abc' | left(-1)}
      PAD,
      '' ],

    [ 'a negative right count is nothing, not an offset',
      <<<'PAD'
      {echo 'abc' | right(-1)}
      PAD,
      '' ],

    [ 'a mid start below one is read as one',
      <<<'PAD'
      {echo 'abc' | mid(0, 1)}
      PAD,
      'a' ],

    [ 'a negative mid length is nothing',
      <<<'PAD'
      {echo 'abc' | mid(2, -1)}
      PAD,
      '' ],

    [ 'an empty delimiter is found at the start - after gives everything',
      <<<'PAD'
      {echo 'abc' | after('')}
      PAD,
      'abc' ],

    [ 'and before it there is nothing',
      <<<'PAD'
      {echo 'abc' | before('')}
      PAD,
      '' ],

    [ 'an empty delimiter is found last at the end - afterLast gives nothing',
      <<<'PAD'
      {echo 'abc' | afterLast('')}
      PAD,
      '' ],

    [ 'and beforeLast gives everything',
      <<<'PAD'
      {echo 'abc' | beforeLast('')}
      PAD,
      'abc' ],

  ];

?>