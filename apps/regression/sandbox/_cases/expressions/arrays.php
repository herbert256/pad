<?php

  // An array where an operator expects a value.
  //
  // Three files decide this - eval/go/doubleArrArr.php, doubleArrVar.php and doubleVarArr.php -
  // and until now most of what reaches them raised a 'ToDo' error and ended the request. The
  // rules they follow are worth pinning because none of them is forced:
  //
  //   - two arrays compare whole under eq and ne, and under PHP's array order for lt le gt ge:
  //     the shorter is the smaller, and two of one size are compared element by element
  //   - two arrays under and, or and xor read as singleArr.php reads one, an empty array being
  //     false and any populated one true
  //   - anywhere a single value is needed, an array of numbers becomes their sum and any other
  //     array becomes its leaves run together
  //   - against a scalar the sum is the only reduction allowed, and only when the scalar is a
  //     number too; anything else is an error, so a case for it cannot be written here
  //   - an array of one is unwrapped to that element first, whatever it holds
  //
  // ** is here as well. It was tokenised and ranked from the start but never computed, so it
  // rendered itself instead of a power.

  return [

    [ 'two arrays are equal by their contents',
      <<<'PAD'
      {if [1,2] eq [1,2]}
        y
      {/if}
      PAD,
      'y' ],

    [ 'the shorter array is the smaller',
      <<<'PAD'
      {if [1,2] lt [1,2,3]}
        y
      {/if}
      PAD,
      'y' ],

    [ 'arrays of one size compare element by element',
      <<<'PAD'
      {if [1,9] gt [1,2]}
        y
      {/if}
      PAD,
      'y' ],

    [ 'an empty array is false, a populated one true',
      <<<'PAD'
      {if [1] and [2]}
        a
      {/if}
      {if [] or []}
        b
      {/if}
      {if [] xor [1]}
        c
      {/if}
      PAD,
      'ac' ],

    [ 'an array of numbers sums where one value is needed',
      <<<'PAD'
      {echo '' | [1,2] + [3,4]}
      PAD,
      '10' ],

    [ 'any other array runs its elements together',
      <<<'PAD'
      {echo '' | ['a','b'] . ['c']}
      PAD,
      'abc' ],

    [ 'an array against a number sums first',
      <<<'PAD'
      {if [1,2,3] eq 6}
        y
      {/if}
      PAD,
      'y' ],

    [ 'the number may stand on the left',
      <<<'PAD'
      {echo '' | 10 - [1,2]}
      PAD,
      '7' ],

    [ 'an array of one is unwrapped, not summed',
      <<<'PAD'
      {echo '' | ['x'] . 'y'}
      PAD,
      'xy' ],

    [ '** raises to a power',
      <<<'PAD'
      {if 2 ** 10 eq 1024}
        y
      {/if}
      PAD,
      'y' ],

    [ '** binds tighter than *',
      <<<'PAD'
      {if 2 * 3 ** 2 eq 18}
        y
      {/if}
      PAD,
      'y' ],

  ];

?>
