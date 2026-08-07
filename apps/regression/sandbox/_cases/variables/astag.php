<?php

  // A page variable written as a tag, which is the second half of the manual's "Tag return
  // values" page - the first half is a PHP function of that name, and tags/returns.php has it.
  //
  // padTypeTagCheck answers 'field' for a name padFieldCheck knows, so {myVar} is the variable
  // and what it holds decides what the level does. The six kinds are the ones the manual lists,
  // and they are not six ways of saying the same thing: a value prints itself and counts as a
  // hit, an array is iterated, and NULL, FALSE and an empty array are three different misses -
  // level/flags.php tells them apart, which is why null= and else= are separate options.
  //
  // Each case defines its variable through the fourth element, which is the only way a case can
  // have a page variable at all.

  return [

    [ 'a value prints itself',
      <<<'PAD'
      {vValue}
      PAD,
      'hello',
      [ 'vValue' => 'hello' ] ],

    [ 'and counts as a hit, so the content follows it',
      <<<'PAD'
      {vValue}T@else@F{/vValue}
      PAD,
      'helloT',
      [ 'vValue' => 'hello' ] ],

    [ 'TRUE renders the content and nothing of its own',
      <<<'PAD'
      {vTrue}T@else@F{/vTrue}
      PAD,
      'T',
      [ 'vTrue' => TRUE ] ],

    [ 'FALSE takes the else branch',
      <<<'PAD'
      {vFalse}T@else@F{/vFalse}
      PAD,
      'F',
      [ 'vFalse' => FALSE ] ],

    [ 'NULL takes neither branch',
      <<<'PAD'
      {vNull}T@else@F{/vNull}
      PAD,
      '',
      [ 'vNull' => NULL ] ],

    [ 'an array is iterated, one occurrence per element',
      <<<'PAD'
      {vList}
        {$vList},
      {/vList}
      PAD,
      '1,2,3,',
      [ 'vList' => [ 1, 2, 3 ] ] ],

    [ 'and counts as a hit',
      <<<'PAD'
      {vList}T@else@F{/vList}
      PAD,
      'TTT',
      [ 'vList' => [ 1, 2, 3 ] ] ],

    [ 'an empty array is a miss, like FALSE and unlike NULL',
      <<<'PAD'
      {vEmpty}T@else@F{/vEmpty}
      PAD,
      'F',
      [ 'vEmpty' => [] ] ],

  ];

?>