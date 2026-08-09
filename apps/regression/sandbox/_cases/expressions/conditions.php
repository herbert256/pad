<?php

  // Property references inside conditions - {if first@items} and its family.
  //
  // The tokeniser reads word@target as one reference when the word has a file in
  // pad/properties/ and a target follows; the token then resolves exactly as the
  // $-spelling always has. The set of names is closed: a word that is not a property keeps
  // the placeholder tokenisation, and the adjacent values merge - the last case pins that.
  //
  // The target names an iterating tag - a level - not a store: count@cw outside {cw}...{/cw}
  // has nothing to answer about and comes back empty, the same as any missing target. The
  // relative -N is accepted by the tokeniser but answers about the levels the at-rules land
  // on, which inside an {if} is rarely what a reader expects - the cases here name their
  // target.

  return [

    [ 'first and last as conditions wrap the loop',
      <<<'PAD'
      {data 'cx'}["a","b","c"]{/data}
      {cx}{if first@cx}[{/if}{$cx}{if last@cx}]{/if}{/cx}
      PAD,
      '[abc]' ],

    [ 'notFirst writes the comma list',
      <<<'PAD'
      {data 'cy'}["a","b","c"]{/data}
      {cy}{if notFirst@cy}, {/if}{$cy}{/cy}
      PAD,
      'a, b, c' ],

    [ 'notLast writes the separator from the other side',
      <<<'PAD'
      {data 'cz'}["a","b","c"]{/data}
      {cz}{$cz}{if notLast@cz}-{/if}{/cz}
      PAD,
      'a-b-c' ],

    [ 'odd and else alternate',
      <<<'PAD'
      {data 'co'}["a","b","c"]{/data}
      {co}{if odd@co}o{else}e{/if}{/co}
      PAD,
      'oeo' ],

    [ 'middle is neither end',
      <<<'PAD'
      {data 'cm'}["a","b","c"]{/data}
      {cm}{if middle@cm}m{else}.{/if}{/cm}
      PAD,
      '.m.' ],

    [ 'border is either end',
      <<<'PAD'
      {data 'cb'}["a","b","c"]{/data}
      {cb}{if border@cb}b{else}.{/if}{/cb}
      PAD,
      'b.b' ],

    [ 'a value property composes with an operator',
      <<<'PAD'
      {data 'cc'}["a","b","c"]{/data}
      {cc}{if current@cc eq 2}={/if}{$cc}{/cc}
      PAD,
      'a=bc' ],

    [ 'count in a comparison, the same on every row',
      <<<'PAD'
      {data 'cn'}["a","b","c"]{/data}
      {cn}{if count@cn eq 3}3{/if}{/cn}
      PAD,
      '333' ],

    [ 'remaining reaches zero on the last row',
      <<<'PAD'
      {data 'cr'}["a","b","c"]{/data}
      {cr}{$cr}{if remaining@cr eq 0}!{/if}{/cr}
      PAD,
      'abc!' ],

    [ 'two properties joined with or',
      <<<'PAD'
      {data 'ce'}["a","b","c"]{/data}
      {ce}{if first@ce or last@ce}E{else}.{/if}{/ce}
      PAD,
      'E.E' ],

    [ 'two properties joined with and',
      <<<'PAD'
      {data 'ca'}["a","b","c"]{/data}
      {ca}{if notFirst@ca and notLast@ca}M{else}.{/if}{/ca}
      PAD,
      '.M.' ],

    [ 'a property and a field in one condition',
      <<<'PAD'
      {data 'cf'}["a","b","c"]{/data}
      {cf}{if $cf eq 'b' and notFirst@cf}Y{else}.{/if}{/cf}
      PAD,
      '.Y.' ],

    [ 'properties drive an elseif chain',
      <<<'PAD'
      {data 'ch'}["a","b","c"]{/data}
      {ch}{if first@ch}first {elseif last@ch}last{else}mid {/if}{/ch}
      PAD,
      'first mid last' ],

    [ 'a property assigns through set',
      <<<'PAD'
      {data 'cw'}["a","b","c"]{/data}
      {cw}{if first@cw}{set $cwN = count@cw/}{/if}{/cw}
      [{$cwN}]
      PAD,
      '[3]' ],

    [ 'a property with a missing target answers empty',
      <<<'PAD'
      {if count@nowhere eq ''}none{/if}
      PAD,
      'none' ],

    [ 'the reference feeds the placeholder',
      <<<'PAD'
      {data 'cp'}["a","b","c"]{/data}
      {cp}{echo current@cp | @ * 10},{/cp}
      PAD,
      '10,20,30,' ],

    [ 'a quoted @ is still just a character',
      <<<'PAD'
      {echo 'a@b'}
      PAD,
      'a@b' ],

    [ 'the bare placeholder is untouched',
      <<<'PAD'
      {echo 50 | @ + 1}
      PAD,
      '51' ],

    [ 'the property ternary answers as before',
      <<<'PAD'
      {data 'ct'}["a","b","c"]{/data}
      {ct}{first@ct ? Y : N}{/ct}
      PAD,
      'YNN' ],

    [ 'a word that is not a property keeps the placeholder reading',
      <<<'PAD'
      {echo 'x' | zz@yy}
      PAD,
      'zzxyy' ],

    // The sigil decides what a colliding name means: rows here carry fields named 'first'
    // and 'count', and the bare spelling still answers the iteration state while the
    // $-spelling answers the row. Each case defines its own data - the stores do not
    // survive from one case to the next.

    [ 'a field named first does not shadow the bare property',
      <<<'PAD'
      {data 'sh1' ignore}[{"first":"Dave"},{"first":""},{"first":"Eve"}]{/data}
      {sh1}{if first@sh1}F{else}.{/if}{/sh1}
      PAD,
      'F..' ],

    [ 'the dollar spelling reads that field',
      <<<'PAD'
      {data 'sh2' ignore}[{"first":"Dave"},{"first":""},{"first":"Eve"}]{/data}
      {sh2}{$first@sh2},{/sh2}
      PAD,
      'Dave,,Eve,' ],

    [ 'and compares it',
      <<<'PAD'
      {data 'sh3' ignore}[{"first":"Dave"},{"first":""},{"first":"Eve"}]{/data}
      {sh3}{if $first@sh3 eq 'Dave'}D{else}.{/if}{/sh3}
      PAD,
      'D..' ],

    [ 'the dollar spelling tests the field truth, not the row position',
      <<<'PAD'
      {data 'sh4' ignore}[{"first":"Dave"},{"first":""},{"first":"Eve"}]{/data}
      {sh4}{if $first@sh4}t{else}f{/if}{/sh4}
      PAD,
      'tft' ],

    [ 'a field named count does not shadow the bare count',
      <<<'PAD'
      {data 'sh5' ignore}[{"count":"9"},{"count":"9"},{"count":"9"}]{/data}
      {sh5}{if count@sh5 eq 3}3{/if}{/sh5}
      PAD,
      '333' ],

    [ 'while the dollar count is the field',
      <<<'PAD'
      {data 'sh6' ignore}[{"count":"9"},{"count":"9"},{"count":"9"}]{/data}
      {sh6}{$count@sh6}{/sh6}
      PAD,
      '999' ],

    [ 'both sigils stand in one condition',
      <<<'PAD'
      {data 'sh7' ignore}[{"first":"Dave"},{"first":""},{"first":"Eve"}]{/data}
      {sh7}{if first@sh7 and $first@sh7 eq 'Dave'}Y{else}.{/if}{/sh7}
      PAD,
      'Y..' ],

    [ 'a shadowed set still assigns the property',
      <<<'PAD'
      {data 'sh8' ignore}[{"count":"9"},{"count":"9"},{"count":"9"}]{/data}
      {sh8}{if first@sh8}{set $sh8N = count@sh8/}{/if}{/sh8}
      [{$sh8N}]
      PAD,
      '[3]' ],

    [ 'key as a bare property under an operator',
      <<<'PAD'
      {data 'sh9'}["p","q","r"]{/data}
      {sh9}{if key@sh9 eq 1}K{else}.{/if}{/sh9}
      PAD,
      '.K.' ],

    [ 'name answers the tag it iterates as',
      <<<'PAD'
      {data 'shA'}["p","q","r"]{/data}
      {shA}{if name@shA eq 'shA'}N{/if}{/shA}
      PAD,
      'NNN' ],

  ];

?>
