<?php

  // The @ reference groups and types no case elsewhere reaches: all and any search without
  // naming a place, saved reaches the value a level shadowed, providers what a {reactData}
  // provider returned, sequences the sequence subsystem's store.
  //
  // Three of the group names have no case, because no spelling of them yields a value a
  // template can assert. The level group (the whole data set of a level) returns INF for
  // every path tried, numeric, dotted or named; the function group captures the driving PHP
  // function's locals, and the storable-name filter leaves none standing in any mode a suite
  // can reach; the data@ property resolves internally, but an {echo} of it turns the array
  // into iteration data that renders nothing, and no reduction spelling brings the values
  // out. The pinned empty lines in regression2's catalog/properties watch all three.

  return [

    [ 'all searches everywhere',
      <<<'PAD'
      {echo $caseAllVar@all}
      PAD,
      'found',
      [ 'caseAllVar' => 'found' ] ],

    [ 'any searches every store of a level',
      <<<'PAD'
      {true $caseAnyVar='9'}
        {echo $caseAnyVar@any}
      {/true}
      PAD,
      '9' ],

    [ 'saved reaches the value an occurrence variable shadowed',
      <<<'PAD'
      {data 'sv'}
        ["x"]
      {/data}
      {sv %savedFixture='inner'}
        {$savedFixture}/{echo $savedFixture@saved}
      {/sv}
      PAD,
      'inner/outer',
      'scope' ],

    [ 'providers reaches what a provider returned',
      <<<'PAD'
      {echo $caseProv.id@providers}
      PAD,
      '7',
      [ 'padProviders' => [ 'caseProv' => [ 'id' => 7 ] ] ] ],

    [ 'sequences searches the store - an array path, so the index counts from 0 where sequence:name(n) counts terms from 1',
      <<<'PAD'
      {sequence '1..5', push='caseSeq'/}
      {echo $caseSeq.3@sequences}
      PAD,
      '4' ],

    [ 'options reaches the named options of a level',
      <<<'PAD'
      {true opt='v'}
        {echo $opt@options}
      {/true}
      PAD,
      'v' ],

    [ 'parameters reaches the positional parameters of a level',
      <<<'PAD'
      {true 'p1', 'p2'}
        {echo $2@parameters}
      {/true}
      PAD,
      'p2' ],

    [ 'tags searches every open level',
      <<<'PAD'
      {true $t='7'}
        {echo $t@tags}
      {/true}
      PAD,
      '7' ],




    [ 'option as a property, named after the dot',
      <<<'PAD'
      {true opt='v'}
        {echo $option.opt@true}
      {/true}
      PAD,
      'v' ],

    [ 'parameter as a property, numbered after the dot',
      <<<'PAD'
      {true 'p1', 'p2'}
        {echo $parameter.2@true}
      {/true}
      PAD,
      'p2' ],

    [ 'variable as a property, named after the dot',
      <<<'PAD'
      {true $t='9'}
        {echo $variable.t@true}
      {/true}
      PAD,
      '9' ],

  ];

?>
