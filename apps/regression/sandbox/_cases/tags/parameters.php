<?php

  // What a tag can be given, and how it reads it back.
  //
  // A parameter is written after the name and before any option, separated by a comma; the first
  // one reaches a tag handler as $padParm and all of them through padTagParm(). The value is an
  // expression, not a literal, so a variable or a calculation may stand there.
  //
  // Reading them back inside the tag has two spellings that mean the same thing - {parm:name} and
  // the short {#name} - and the properties a tag publishes about itself have the same pair,
  // {property:name} and {&name}. Both shorthands are resolved by padEvalAfter rather than by a
  // type handler, which is why they are worth a case of their own.

  return [

    [ 'a quoted parameter',
      <<<'PAD'
      {withparm label='hi'/}
      PAD,
      'hi' ],

    [ 'double quotes, and a space inside',
      <<<'PAD'
      {withparm label="hi there"/}
      PAD,
      'hi there' ],

    [ 'a number needs no quotes',
      <<<'PAD'
      {withparm label=42/}
      PAD,
      '42' ],

    [ 'a variable as the value',
      <<<'PAD'
      {set $v = 'zz'/}
      {withparm label=$v/}
      PAD,
      'zz' ],

    [ 'an expression as the value',
      <<<'PAD'
      {withparm label='a' . 'b'/}
      PAD,
      'ab' ],

    [ 'a second parameter the tag does not read',
      <<<'PAD'
      {withparm label='hi', other='no'/}
      PAD,
      'hi' ],

    [ 'a tag that declares a default uses it',
      <<<'PAD'
      {box/}
      PAD,
      '[none]' ],

    [ 'and the parameter overrides the default',
      <<<'PAD'
      {box label='q'/}
      PAD,
      '[q]' ],

    [ 'a built-in tag takes several parameters',
      <<<'PAD'
      {echo 'a', 'b'}
      PAD,
      'ab' ],

    [ 'parm: reads a parameter from inside the tag',
      <<<'PAD'
      {sequence '1..3', name='s'}
        {parm:name},
      {/sequence}
      PAD,
      's,s,s,' ],

    [ 'the # shorthand is the same lookup',
      <<<'PAD'
      {sequence '1..3', name='s'}
        {#name},
      {/sequence}
      PAD,
      's,s,s,' ],

    [ 'property: reads what the tag publishes',
      <<<'PAD'
      {sequence '1..3', name='s'}
        {property:current},
      {/sequence}
      PAD,
      '1,2,3,' ],

    [ 'the & shorthand is the same lookup',
      <<<'PAD'
      {sequence '1..3', name='s'}
        {&current},
      {/sequence}
      PAD,
      '1,2,3,' ],

    // A variable written on the tag itself. $ is one value for the whole run and % is drawn again
    // for each occurrence; both are read back as ordinary fields, with $.

    [ 'a level variable holds for every occurrence',
      <<<'PAD'
      {data 'x'}
        [10,20]
      {/data}
      {x $t = 'k'}
        {$t}{$x},
      {/x}
      PAD,
      'k10,k20,' ],

    [ 'an occurrence variable is worked out per row',
      <<<'PAD'
      {data 'x'}
        [10,20]
      {/data}
      {x %d = $x * 2}
        {$d},
      {/x}
      PAD,
      '20,40,' ],

  ];

?>