<?php

  // The single {tag/} form, which has to mean the same thing however it is spaced.
  //
  // It did not. level/level.php resolves the tag name before level/tag.php takes the trailing
  // slash off, and with no options at all the slash was part of the name - {box/} was looked up as
  // a tag called "box/" and nothing claimed it, while {box label='hi'/} worked because there the
  // slash sat at the end of the options. level/tag.php resolves the name again now.
  //
  // So the cases come in pairs: the same tag with options and without, with the space and without,
  // and against the written-out pair. All four have to agree.

  return [

    [ 'a tag with options closes on /}',
      <<<'PAD'
      {box label='hi'/}
      PAD,
      '[hi]' ],

    [ 'a tag without options closes on /} as well',
      <<<'PAD'
      {box/}
      PAD,
      '[none]' ],

    [ 'the space before the slash is optional',
      <<<'PAD'
      {box /}
      PAD,
      '[none]' ],

    [ 'the same tag written out as a pair',
      <<<'PAD'
      {box}{/box}
      PAD,
      '[none]' ],

    [ 'a built-in tag, no options, no space',
      <<<'PAD'
      {true/}
      PAD,
      '' ],

    [ 'a built-in tag, no options, with space',
      <<<'PAD'
      {true /}
      PAD,
      '' ],

    [ 'options and a slash on a built-in tag',
      <<<'PAD'
      {set $a = 1/}
      {$a}
      PAD,
      '1' ],

    [ 'a name nothing claims is still left standing',
      <<<'PAD'
      {nosuchtag/}
      PAD,
      '{nosuchtag}' ],

  ];

?>
