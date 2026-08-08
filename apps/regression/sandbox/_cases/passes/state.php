<?php

  // What crosses the boundary of a nested pass, in each direction.
  //
  // The contracts these hold have each been broken once: a sandboxed pass empties the four
  // stores going in and restores them coming out, an enclosing iteration keeps its place
  // across a pass, a prefixed sequence pair keeps its closing tag inside one, and a
  // request-lived counter such as {switch}'s neither escapes an isolated pass nor loses
  // its place to a plain one.

  return [

    [ 'a clean pass sees the store outside it',
      <<<'PAD'
      {data 'psSee'}["x","y"]{/data}
      {code clean}{psSee}{$psSee}{/psSee}{/code}
      PAD,
      'xy' ],

    [ 'a sandboxed pass starts without it',
      <<<'PAD'
      {data 'psHid'}["x","y"]{/data}
      {code sandbox}{psHid}M{/psHid}{/code}
      PAD,
      '{psHid}M{/psHid}' ],

    [ 'a plain pass leaves its push behind',
      <<<'PAD'
      {code}{sequence '7;8', push='psOut'/}in{/code}
      [{pull:psOut}{$sequence},{/pull:psOut}]
      PAD,
      'in[7,8,]' ],

    [ 'a sandboxed push is gone after the pass',
      <<<'PAD'
      {code sandbox}{sequence '7;8', push='psGone'/}in{/code}
      [{pull:psGone}M{/pull:psGone}]
      PAD,
      'in[{pull:psGone}M{/pull:psGone}]' ],

    [ 'an enclosing iteration keeps its place across a sandboxed pass',
      <<<'PAD'
      {data 'psRow'}["a","b","c"]{/data}
      {psRow}{code sandbox}p{/code}{$psRow},{/psRow}
      PAD,
      'pa,pb,pc,' ],

    [ 'a prefixed sequence pair keeps its closing tag inside a pass',
      <<<'PAD'
      {code}{prime:keep rows=3, from=10}{$sequence},{/prime:keep}{/code}
      PAD,
      '11,13,17,' ],

    [ 'a switch rotation is engine state - a plain pass rolls it back',
      <<<'PAD'
      {code}{switch 'x','y'}{/code}{switch 'x','y'}
      PAD,
      'xx' ],

    [ 'a sandboxed pass the same',
      <<<'PAD'
      {code sandbox}{switch 'p','q'}{/code}{switch 'p','q'}
      PAD,
      'pp' ],

    [ 'a function pass copies what it created out',
      <<<'PAD'
      {code function='psFn1'}{set $psV1 = 'q'/}F{/code}
      [{$psV1 | optional}]
      PAD,
      'F[q]' ],

    [ 'a sandboxed function pass does not',
      <<<'PAD'
      {code function='psFn2', sandbox}{set $psV2 = 'q'/}F{/code}
      [{$psV2 | optional}]
      PAD,
      'F[]' ],

  ];

?>
