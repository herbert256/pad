<?php

  // A pipe written on the opening tag and a pipe written on the closing tag, which are not the
  // same thing and do not act on the same text.
  //
  //   level/pipes/before.php  runs the opening pipe over $padBase [$pad], the level's content as
  //                           the tag handler left it, before a single occurrence is rendered.
  //                           What it transforms is therefore the template, not the values.
  //
  //   level/pipes/after.php   runs the closing pipe over $padResult [$pad], every occurrence
  //                           already rendered and joined.
  //
  // That distinction is easy to get wrong, and CLAUDE.md had it wrong: it gave {items | sort} as
  // the way to sort before rendering. An opening pipe cannot do that - it would hand the literal
  // template text to the function, and sort() raises a PHP error on being given a string at all.
  // Sorting is the sort option, {items sort}, which is the last case here.

  return [

    [ 'a closing pipe transforms everything the level rendered',
      <<<'PAD'
      {data 'x'}
        ["b","a"]
      {/data}
      {x}
        {$x},
      {/x | upper}
      PAD,
      'B,A,' ],

    // Chained left to right, each over what the one before it returned - so upper followed by
    // lower ends lower, and the same pair the other way round ends upper.

    [ 'closing pipes chain, each over the last result',
      <<<'PAD'
      {data 'x'}
        ["b","a"]
      {/data}
      {x}
        {$x},
      {/x | upper | lower}
      PAD,
      'b,a,' ],

    [ 'a closing pipe over a tag that renders once',
      <<<'PAD'
      {echo ' ab '}
      {/echo | trim | upper}
      PAD,
      'AB' ],

    // An opening pipe rewrites the content template itself, once, before any occurrence runs -
    // so upper here uppercases the literal text and it comes out uppercased for every row. A
    // field written inside would be uppercased too, and then not found.

    [ 'an opening pipe transforms the template, not the values',
      <<<'PAD'
      {data 'x'}
        ["b","a"]
      {/data}
      {x | upper}
        ab,
      {/x}
      PAD,
      'AB,AB,' ],

    [ 'both ends at once, each on its own text',
      <<<'PAD'
      {data 'x'}
        ["b","a"]
      {/data}
      {x | trim}
        y,
      {/x | upper}
      PAD,
      'Y,Y,' ],

    [ 'sorting is an option, not an opening pipe',
      <<<'PAD'
      {data 'x'}
        ["b","a"]
      {/data}
      {x sort}
        {$x},
      {/x | upper}
      PAD,
      'A,B,' ],

  ];

?>