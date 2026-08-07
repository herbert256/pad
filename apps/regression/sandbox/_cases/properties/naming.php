<?php

  // Reading the properties of a tag that is not the nearest one.
  //
  // Written plainly, {first} answers about the level it stands in. The manual gives three ways to
  // reach past that, and each has two spellings - as a tag, and as a & variable:
  //
  //   by name             {first 'staff'}   {if &staff:first}
  //   by relative level   {first -2}        {if &-2:first}
  //
  // All four are checked over the same data, with a {true} between the loop and the property so
  // that the nearest level is deliberately the wrong one to ask.

  return [

    [ 'a property names the tag it belongs to',
      <<<'PAD'
      {data 's'}
        ["a","b","c"]
      {/data}
      {s}
        {true}
          {$s}{first 's'}!{/first}
        {/true}
      {/s}
      PAD,
      'a!bc' ],

    [ 'the same as a variable, with the tag as prefix',
      <<<'PAD'
      {data 's'}
        ["a","b","c"]
      {/data}
      {s}
        {true}
          {$s}{if &s:first}!{/if}
        {/true}
      {/s}
      PAD,
      'a!bc' ],

    [ 'or by how many levels up it is',
      <<<'PAD'
      {data 's'}
        ["a","b","c"]
      {/data}
      {s}
        {true}
          {$s}{first -2}!{/first}
        {/true}
      {/s}
      PAD,
      'a!bc' ],

    [ 'and that as a variable too',
      <<<'PAD'
      {data 's'}
        ["a","b","c"]
      {/data}
      {s}
        {true}
          {$s}{if &-2:first}!{/if}
        {/true}
      {/s}
      PAD,
      'a!bc' ],

    [ 'last reaches as far as first does',
      <<<'PAD'
      {data 's'}
        ["a","b","c"]
      {/data}
      {s}
        {true}
          {$s}{last 's'}#{/last}
        {/true}
      {/s}
      PAD,
      'abc#' ],

  ];

?>