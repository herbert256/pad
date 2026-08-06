<?php

  // Comparison, arithmetic and the @ placeholder.
  //
  // Each operator is checked where it differs from its neighbour rather than where any answer
  // would do: ge and le at the boundary they include, and with one side false, range just inside
  // and just outside, precedence against the same expression in parentheses.

  return [

    [ 'eq on strings',
      <<<'PAD'
      {if 'a' eq 'a'}
        y
      {/if}
      PAD,
      'y' ],

    [ 'ne',
      <<<'PAD'
      {if 1 ne 2}
        y
      {/if}
      PAD,
      'y' ],

    [ 'the != spelling',
      <<<'PAD'
      {if 1 != 2}
        y
      {/if}
      PAD,
      'y' ],

    [ 'gt and lt',
      <<<'PAD'
      {if 5 gt 3 and 2 lt 4}
        y
      {/if}
      PAD,
      'y' ],

    [ 'ge and le at the boundary',
      <<<'PAD'
      {if 5 ge 5 and 5 le 5}
        y
      {/if}
      PAD,
      'y' ],

    [ 'and is not or',
      <<<'PAD'
      {if 1 eq 1 and 1 eq 2}
        y
      {/if}
      PAD,
      '' ],

    [ 'or takes either',
      <<<'PAD'
      {if 1 eq 2 or 2 eq 2}
        y
      {/if}
      PAD,
      'y' ],

    [ 'not inverts',
      <<<'PAD'
      {if not 1 eq 2}
        y
      {/if}
      PAD,
      'y' ],

    [ 'a number equals its own spelling',
      <<<'PAD'
      {if '10' eq 10}
        y
      {/if}
      PAD,
      'y' ],

    [ 'range includes the value',
      <<<'PAD'
      {if 30 range (20, 40)}
        in
      {/if}
      PAD,
      'in' ],

    [ 'range excludes what is outside',
      <<<'PAD'
      {if 50 range (20, 40)}
        in
      {/if}
      PAD,
      '' ],

    [ 'arithmetic keeps precedence',
      <<<'PAD'
      {echo 2 + 3 * 4}
      PAD,
      '14' ],

    [ 'parentheses override it',
      <<<'PAD'
      {echo (2 + 3) * 4}
      PAD,
      '20' ],

    [ 'modulo',
      <<<'PAD'
      {echo 7 % 3}
      PAD,
      '1' ],

    [ 'a negative result',
      <<<'PAD'
      {echo 0 - 5}
      PAD,
      '-5' ],

    [ 'fractions add up',
      <<<'PAD'
      {echo 1.5 + 2.5}
      PAD,
      '4' ],

    [ 'division keeps the fraction',
      <<<'PAD'
      {echo 7 / 2}
      PAD,
      '3.5' ],

    [ 'concatenation',
      <<<'PAD'
      {echo 'a' . 'b'}
      PAD,
      'ab' ],

    [ 'concatenation with a variable',
      <<<'PAD'
      {set $a = 'x'/}
      {echo $a . 'y'}
      PAD,
      'xy' ],

    [ 'the @ placeholder is the piped value',
      <<<'PAD'
      {echo 50 | @ * 4}
      PAD,
      '200' ],

    [ '@ inside a built string',
      <<<'PAD'
      {echo 'x' | '"' . @ . '"'}
      PAD,
      '"x"' ],

    [ 'an expression over a variable',
      <<<'PAD'
      {set $n = 4/}
      {echo $n * $n}
      PAD,
      '16' ],

  ];

?>
