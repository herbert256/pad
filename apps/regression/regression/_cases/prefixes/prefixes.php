<?php

  // The prefixes that say what a name means, for the cases where a bare name would be ambiguous.
  //
  // Every prefix that resolves inside this application is here, including the ones that need files
  // it ships - app: and include: read the fixtures in _tags/ and _include/, local: reads _data/ -
  // and the sequence prefixes, which reach the subsystem the sequence group covers in depth.

  return [

    [ 'php: calls a php function',
      <<<'PAD'
      {echo php:strtoupper('ab')}
      PAD,
      'AB' ],

    [ 'php: with a number',
      <<<'PAD'
      {echo php:strlen('abcd')}
      PAD,
      '4' ],

    [ 'php: over a variable',
      <<<'PAD'
      {set $s = 'ab'/}
      {echo php:strrev($s)}
      PAD,
      'ba' ],

    [ 'php: nested in php:',
      <<<'PAD'
      {echo php:strtoupper(php:trim('  a  '))}
      PAD,
      'A' ],

    [ 'constant: reads a php constant',
      <<<'PAD'
      {echo constant:PHP_INT_SIZE}
      PAD,
      '8' ],

    [ 'constant: reads a big one',
      <<<'PAD'
      {echo constant:PHP_INT_MAX}
      PAD,
      '9223372036854775807' ],

    [ 'pad: names the built-in tag',
      <<<'PAD'
      {pad:if 1 eq 1}
        y
      {/pad:if}
      PAD,
      'y' ],

    [ 'data: names the data store',
      <<<'PAD'
      {data 'xs'}
        [1,2]
      {/data}
      {data:xs}
        {$xs},
      {/data:xs}
      PAD,
      '1,2,' ],

    [ 'content: reads a content store',
      <<<'PAD'
      {content 'k'}
        hello
      {/content}
      {content:k}
      PAD,
      'hello' ],

    [ 'bool: reads a boolean store',
      <<<'PAD'
      {bool 'b'}
        1
      {/bool}
      {bool:b}
        yes
      {/bool:b}
      PAD,
      'yes' ],

    [ 'local: reads a file from _data/',
      <<<'PAD'
      {local:nums.json}
        {$nums},
      {/local:nums.json}
      PAD,
      '11,22,33,' ],

    [ 'app: names an application tag',
      <<<'PAD'
      {app:box label='hi'/}
      PAD,
      '[hi]' ],

    [ 'include: names a snippet',
      <<<'PAD'
      {set $who = 'bob'/}
      {include:fixture}
      PAD,
      'snippet:bob' ],

    [ 'pull: names a stored sequence',
      <<<'PAD'
      {sequence '1..3', push='p'/}
      {pull:p}
        {$sequence},
      {/pull:p}
      PAD,
      '1,2,3,' ],

    [ 'sequence: names a sequence type',
      <<<'PAD'
      {sequence:prime rows=3}
        {$sequence},
      {/sequence:prime}
      PAD,
      '2,3,5,' ],

    [ 'keep: names a play over a type',
      <<<'PAD'
      {keep:prime rows=4, from=10}
        {$sequence},
      {/keep:prime}
      PAD,
      '11,13,17,19,' ],

  ];

?>
