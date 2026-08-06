<?php

  // The extension points an application supplies: _tags/, _functions/, _include/ and _data/.
  //
  // The fixtures these read are shipped beside the suite - _tags/box.php, _functions/exclaim.php,
  // _include/fixture.pad and _data/nums.json - and nothing else uses them, so a case here fails
  // only when the lookup itself changes.
  //
  // Each is checked twice where the engine offers two spellings: the bare name and the explicit
  // prefix, which resolve through different code and are only supposed to agree.

  return [

    [ 'an application tag from _tags/',
      <<<'PAD'
      {box label='hi'/}
      PAD,
      '[hi]' ],

    [ 'the tag falls back to its default parameter',
      <<<'PAD'
      {box}
      PAD,
      '[none]' ],

    [ 'the same tag written as a pair',
      <<<'PAD'
      {box}{/box}
      PAD,
      '[none]' ],

    [ 'app: names it explicitly',
      <<<'PAD'
      {app:box label='hi'/}
      PAD,
      '[hi]' ],

    [ 'an application tag inside a loop',
      <<<'PAD'
      {data 'xs'}
        [1,2]
      {/data}
      {xs}
        {box label='r'/}
      {/xs}
      PAD,
      '[r][r]' ],

    [ 'a pipe function from _functions/',
      <<<'PAD'
      {echo 'a' | exclaim}
      PAD,
      'a!' ],

    [ 'a custom function in a chain',
      <<<'PAD'
      {echo 'a' | exclaim | upper}
      PAD,
      'A!' ],

    [ 'a snippet from _include/',
      <<<'PAD'
      {set $who = 'bob'/}
      {fixture}
      PAD,
      'snippet:bob' ],

    [ 'include: names it explicitly',
      <<<'PAD'
      {set $who = 'bob'/}
      {include:fixture}
      PAD,
      'snippet:bob' ],

    [ 'a snippet sees the variables around it',
      <<<'PAD'
      {set $who = 'amy'/}
      {fixture}
      PAD,
      'snippet:amy' ],

    [ 'local: reads a file from _data/',
      <<<'PAD'
      {local:nums.json}
        {$nums},
      {/local:nums.json}
      PAD,
      '11,22,33,' ],

  ];

?>
