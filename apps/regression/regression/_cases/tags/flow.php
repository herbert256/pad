<?php

  // Control flow: the tags that decide what is rendered and how often.
  //
  // The branch a condition does not take must produce nothing, a loop must run exactly as many
  // times as its condition allows, and the three loop controls must differ - break leaves at
  // once, cease finishes the row it is in, continue skips only the rest of that row.
  //
  // Both spellings of the default branch are covered: the @else@ marker, which level/split.php
  // separates out, and the {else} tag, which {if} and {case} now take themselves. {else} used
  // to be documented but absent - there is no tags/else.php - so it was left in the page as an
  // unclaimed name and both branches rendered. Each is checked on both sides of its condition
  // and once inside a nested if, where the wrong one would be easy to pick up.

  return [

    [ 'if, condition true',
      <<<'PAD'
      {if 1 eq 1}
        yes
      {/if}
      PAD,
      'yes' ],

    [ 'if, condition false',
      <<<'PAD'
      {if 1 eq 2}
        yes
      {/if}
      PAD,
      '' ],

    [ 'if with @else@, true',
      <<<'PAD'
      {if 1 eq 1}
        A
      @else@
        B
      {/if}
      PAD,
      'A' ],

    [ 'if with @else@, false',
      <<<'PAD'
      {if 1 eq 2}
        A
      @else@
        B
      {/if}
      PAD,
      'B' ],

    [ 'elseif, the first branch wins',
      <<<'PAD'
      {if 1 eq 1}
        A
      {elseif 1 eq 2}
        B
      {/if}
      PAD,
      'A' ],

    [ 'elseif, the second branch',
      <<<'PAD'
      {if 1 eq 2}
        A
      {elseif 1 eq 1}
        B
      {/if}
      PAD,
      'B' ],

    [ 'elseif, neither branch',
      <<<'PAD'
      {if 1 eq 2}
        A
      {elseif 1 eq 3}
        B
      {/if}
      PAD,
      '' ],

    [ 'a chain of elseif',
      <<<'PAD'
      {if 1 eq 2}
        A
      {elseif 1 eq 3}
        B
      {elseif 1 eq 1}
        C
      {/if}
      PAD,
      'C' ],

    [ 'elseif falling through to @else@',
      <<<'PAD'
      {if 1 eq 2}
        A
      {elseif 1 eq 3}
        B
      @else@
        C
      {/if}
      PAD,
      'C' ],

    [ 'an {else} tag, condition true',
      <<<'PAD'
      {if 1 eq 1}
        A
      {else}
        B
      {/if}
      PAD,
      'A' ],

    [ 'an {else} tag, condition false',
      <<<'PAD'
      {if 1 eq 2}
        A
      {else}
        B
      {/if}
      PAD,
      'B' ],

    [ 'elseif falling through to an {else} tag',
      <<<'PAD'
      {if 1 eq 2}A{elseif 1 eq 3}B{else}C{/if}
      PAD,
      'C' ],

    [ 'an {else} belonging to a nested if',
      <<<'PAD'
      {if 1 eq 1}{if 2 eq 3}X{else}Y{/if}{else}Z{/if}
      PAD,
      'Y' ],

    [ 'case falls to an {else} tag',
      <<<'PAD'
      {case 'blue'}
        {when 'red'}R
        {when 'green'}G
        {else}O
      {/case}
      PAD,
      'O' ],

    [ 'case with a match ignores its {else}',
      <<<'PAD'
      {case 'red'}
        {when 'red'}R
        {else}O
      {/case}
      PAD,
      'R' ],

    [ 'if on a variable',
      <<<'PAD'
      {set $n = 5/}
      {if $n gt 3}
        big
      {/if}
      PAD,
      'big' ],

    [ 'if and',
      <<<'PAD'
      {if 1 eq 1 and 2 eq 2}
        yes
      {/if}
      PAD,
      'yes' ],

    [ 'if or',
      <<<'PAD'
      {if 1 eq 2 or 2 eq 2}
        yes
      {/if}
      PAD,
      'yes' ],

    [ 'if nested',
      <<<'PAD'
      {if 1 eq 1}
        {if 2 eq 2}
          both
        {/if}
      {/if}
      PAD,
      'both' ],

    [ 'if inside a loop',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs}
        {if $xs gt 1}
          {$xs},
        {/if}
      {/xs}
      PAD,
      '2,3,' ],

    [ 'case matches a when',
      <<<'PAD'
      {case 'red'}
        {when 'red'}stop
        {when 'green'}go
      {/case}
      PAD,
      'stop' ],

    [ 'case takes the first match',
      <<<'PAD'
      {case 'red'}
        {when 'red'}one
        {when 'red'}two
      {/case}
      PAD,
      'one' ],

    [ 'case with no match',
      <<<'PAD'
      {case 'blue'}
        {when 'red'}stop
      {/case}
      PAD,
      '' ],

    [ 'case falls to @else@',
      <<<'PAD'
      {case 'blue'}
        {when 'red'}stop
      @else@
        other
      {/case}
      PAD,
      'other' ],

    [ 'case on a variable',
      <<<'PAD'
      {set $c = 'green'/}
      {case $c}
        {when 'red'}stop
        {when 'green'}go
      {/case}
      PAD,
      'go' ],

    [ 'while counts up',
      <<<'PAD'
      {set $i = 1/}
      {while $i le 3}
        {$i},
        {increment $i/}
      {/while}
      PAD,
      '1,2,3,' ],

    [ 'while never entered',
      <<<'PAD'
      {set $i = 9/}
      {while $i le 3}
        {$i},
      {/while}
      PAD,
      '' ],

    [ 'while accumulating',
      <<<'PAD'
      {set $t = 0/}
      {set $i = 1/}
      {while $i le 4}
        {set $t = $t + $i/}
        {increment $i/}
      {/while}
      {$t}
      PAD,
      '10' ],

    [ 'until counts down',
      <<<'PAD'
      {set $i = 3/}
      {until $i eq 0}
        {$i},
        {decrement $i/}
      {/until}
      PAD,
      '3,2,1,' ],

    [ 'a loop inside a loop',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs}
        {data 'ys'}
          [7,8]
        {/data}
        {ys}
          {$xs}{$ys},
        {/ys}
      {/xs}
      PAD,
      '17,18,27,28,37,38,' ],

    [ 'break leaves at once',
      <<<'PAD'
      {data 'xs'}
        [1,2,3,4]
      {/data}
      {xs}
        {$xs},
        {if $xs eq 2}
          {break 'xs'/}
        {/if}
      {/xs}
      PAD,
      '1,' ],

    [ 'cease finishes the row',
      <<<'PAD'
      {data 'xs'}
        [1,2,3,4]
      {/data}
      {xs}
        {$xs},
        {if $xs eq 2}
          {cease 'xs'/}
        {/if}
      {/xs}
      PAD,
      '1,2,' ],

    [ 'continue skips the rest of a row',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs}
        {if $xs eq 2}
          {continue 'xs'/}
        {/if}
        {$xs},
      {/xs}
      PAD,
      '1,3,' ],

  ];

?>
