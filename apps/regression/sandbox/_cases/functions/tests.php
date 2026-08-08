<?php

  // The functions that answer a question rather than transform a value: '1' for yes, '' for no.
  //
  // The pairs matter more than the singles. range includes its ends and between excludes them, so
  // each is checked at an end and inside; like is anchored at both ends and ignores case, so it is
  // checked with a value that would match if it were not.

  return [

    [ 'contains, present',
      <<<'PAD'
      {echo 'hello world' | contains('world')}
      PAD,
      '1' ],

    [ 'contains, absent',
      <<<'PAD'
      {echo 'hello world' | contains('mars')}
      PAD,
      '' ],

    [ 'contains over a number',
      <<<'PAD'
      {echo 12345 | contains('34')}
      PAD,
      '1' ],

    [ 'in, present',
      <<<'PAD'
      {echo 'active' | in('active', 'pending')}
      PAD,
      '1' ],

    [ 'in, absent',
      <<<'PAD'
      {echo 'closed' | in('active', 'pending')}
      PAD,
      '' ],

    [ 'in over numbers',
      <<<'PAD'
      {echo 3 | in(1, 2, 3)}
      PAD,
      '1' ],

    [ 'in with a single choice',
      <<<'PAD'
      {echo 'a' | in('a')}
      PAD,
      '1' ],

    [ 'like with a percent wildcard',
      <<<'PAD'
      {echo 'test.txt' | like('%.txt')}
      PAD,
      '1' ],

    [ 'like that does not match',
      <<<'PAD'
      {echo 'test.doc' | like('%.txt')}
      PAD,
      '' ],

    [ 'like anchored at the front',
      <<<'PAD'
      {echo 'abc' | like('a%')}
      PAD,
      '1' ],

    [ 'like with no wildcard is an exact test',
      <<<'PAD'
      {echo 'abc' | like('abc')}
      PAD,
      '1' ],

    [ 'like with one underscore per character',
      <<<'PAD'
      {echo 'test123' | like('test___')}
      PAD,
      '1' ],

    [ 'like is anchored at both ends',
      <<<'PAD'
      {echo 'test1234' | like('test___')}
      PAD,
      '' ],

    [ 'like ignores case',
      <<<'PAD'
      {echo 'TEST.TXT' | like('%.txt')}
      PAD,
      '1' ],

    [ 'like counts characters, not bytes',
      <<<'PAD'
      {echo 'é' | like('_')}
      PAD,
      '1' ],

    [ 'range includes its ends',
      <<<'PAD'
      {echo 100 | range(0, 100)}
      PAD,
      '1' ],

    [ 'range outside',
      <<<'PAD'
      {echo 150 | range(0, 100)}
      PAD,
      '' ],

    [ 'between excludes its ends',
      <<<'PAD'
      {echo 17 | between(17, 66)}
      PAD,
      '' ],

    [ 'between inside',
      <<<'PAD'
      {echo 30 | between(17, 66)}
      PAD,
      '1' ],

    [ 'exists finds a file in the application',
      <<<'PAD'
      {echo 'index.php' | exists}
      PAD,
      '1' ],

    [ 'range inside the ends is true',
      <<<'PAD'
      {echo 50 | range(0, 100)}
      PAD,
      '1' ],

    [ 'between outside the ends is false',
      <<<'PAD'
      {echo 5 | between(17, 66)}
      PAD,
      '' ],

  ];

?>