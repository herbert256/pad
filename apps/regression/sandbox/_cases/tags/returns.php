<?php

  // What a tag's return value does to its level, which is the manual's "Tag return values" page.
  //
  // A PHP function the application has defined becomes a tag of its own name - padTypeTagCheck()
  // answers 'php' for anything function_exists() knows - so writing {regTrue} calls regTrue() and
  // the level does whatever its answer says. The functions are in _lib/returns.php, one per kind
  // of value.
  //
  // TRUE, FALSE and NULL are three different outcomes, not two. FALSE takes the @else@ branch;
  // NULL renders nothing at all, neither the content nor the else, because level/flags.php puts
  // the level in its null state instead - which is also why a null= option exists and an else=
  // one would not catch it.

  return [

    [ 'true renders the content',
      <<<'PAD'
      {regTrue}
        yes
      @else@
        no
      {/regTrue}
      PAD,
      'yes' ],

    [ 'false takes the else branch',
      <<<'PAD'
      {regFalse}
        yes
      @else@
        no
      {/regFalse}
      PAD,
      'no' ],

    [ 'null takes neither',
      <<<'PAD'
      {regNull}
        yes
      @else@
        no
      {/regNull}
      PAD,
      '' ],

    [ 'an array is iterated, one occurrence per element',
      <<<'PAD'
      {regList}
        {$regList},
      {/regList}
      PAD,
      '1,2,3,' ],

    [ 'name= says what the field is called',
      <<<'PAD'
      {regList name='line'}
        {$line},
      {/regList}
      PAD,
      '1,2,3,' ],

    [ 'an empty array is a miss, so the else branch runs',
      <<<'PAD'
      {regEmpty name='line'}
        {$line},
      @else@
        none
      {/regEmpty}
      PAD,
      'none' ],

    [ 'a string is printed as it stands',
      <<<'PAD'
      {regString}
      PAD,
      'Hello' ],

  ];

?>