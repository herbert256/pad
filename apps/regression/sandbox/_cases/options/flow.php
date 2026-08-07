<?php

  // The options that decide what a level does when it does not simply render: where its data
  // comes from, what stands in its place when there is none, and what it leaves behind.
  //
  // The three stand-in options are not interchangeable. level/flags.php picks between them by
  // what the tag actually produced - else= for an empty array, FALSE or '', null= for NULL, INF
  // or NaN, notOk= for a level that did not hit or threw - so a template can tell "nothing came
  // back" apart from "it went wrong". Each names a content store, which has to exist before the
  // tag runs.
  //
  // Three options in pad/options/ have no case and cannot have one, each for its own reason:
  // bool= has a handler that no phase list names, so a flag written that way never reaches it;
  // demand= only returns TRUE and nothing reads it; noError= is a deliberately empty file. The
  // suppression that does work is optional, which is the last case here.

  return [

    [ 'data= says where the occurrences come from',
      <<<'PAD'
      {data 'y'}
        ["b","a"]
      {/data}
      {true data='y'}
        {$y},
      {/true}
      PAD,
      'b,a,' ],

    [ 'content= puts a named block in place of the level content',
      <<<'PAD'
      {content 'c'}
        hi
      {/content}
      {true content='c'}
      PAD,
      'hi' ],

    [ 'else= stands in for an empty set',
      <<<'PAD'
      {content 'none'}
        nothing
      {/content}
      {data 'e'}
        []
      {/data}
      {e else='none'}
        {$e}
      {/e}
      PAD,
      'nothing' ],

    [ 'null= stands in for a null, which else= would not catch',
      <<<'PAD'
      {content 'n'}
        NULL
      {/content}
      {null null='n'}
        yes
      {/null}
      PAD,
      'NULL' ],

    [ 'notOk= stands in for a level that did not hit',
      <<<'PAD'
      {content 'oops'}
        bad
      {/content}
      {false notOk='oops'}
        yes
      {/false}
      PAD,
      'bad' ],

    // The two that store rather than print. Both blank the level's own result, so the tag writing
    // them shows nothing and what it put aside is read back by name afterwards.

    [ 'toData= parks the data for {data:name}',
      <<<'PAD'
      {data 'x'}
        ["b","a"]
      {/data}
      {x toData='y'/}
      {data:y}
        {$x},
      {/data:y}
      PAD,
      'b,a,' ],

    [ 'and parks it as the level walked it, not as it arrived',
      <<<'PAD'
      {data 'x'}
        ["b","a"]
      {/data}
      {x sort, toData='y'/}
      {data:y}
        {$x},
      {/data:y}
      PAD,
      'a,b,' ],

    [ 'toBool= records whether anything was produced',
      <<<'PAD'
      {data 'x'}
        ["b","a"]
      {/data}
      {x toBool='f'}
        {$x}
      {/x}
      {bool:f}
        yes
      {/bool:f}
      PAD,
      'bayes' ],

    [ 'and records false when nothing was',
      <<<'PAD'
      {data 'x'}
        []
      {/data}
      {x toBool='f'}
        {$x}
      {/x}
      {bool:f}
        yes
      {/bool:f}
      no
      PAD,
      'no' ],

    [ 'optional swallows a name nothing claims',
      <<<'PAD'
      {nosuchtag optional}
        x
      {/nosuchtag}
      PAD,
      '' ],

  ];

?>
