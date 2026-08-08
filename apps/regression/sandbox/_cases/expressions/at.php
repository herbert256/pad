<?php

  // The @ reference groups and types no case elsewhere reaches: all and any search without
  // naming a place, saved reaches the value a level shadowed, providers what a {reactData}
  // provider returned, sequences the sequence subsystem's store.
  //
  // The level group reaches the whole data set of a level - a dotted path names another
  // row's field from whatever row is current. The function group reads the locals a level's
  // PHP left behind - a custom tag's file, a callback's phases. The data@ property is a
  // data source: the pair iterates the whole set, and toData= stores it, with the fields
  // keeping the source tag's name either way.

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

    [ 'level reaches another row of the set from any row',
      <<<'PAD'
      {data 'lv'}
        ["x","y"]
      {/data}
      {lv}
        {echo $0.lv@level}{echo $1.lv@level},
      {/lv}
      PAD,
      'xy,xy,' ],

    [ 'function reaches what a callback left behind',
      <<<'PAD'
      {data 'n'}
        [1,2,3]
      {/data}
      {n callback='before.php', before}
        {last@n}{echo $sum@function}{/last@n}
      {/n}
      PAD,
      '[6]' ],

    [ 'data as a pair iterates the whole set, fields keeping the source name',
      <<<'PAD'
      {data 'dp'}
        ["x","y"]
      {/data}
      {dp}
        {first@dp}[{data@dp}{$dp},{/data@dp}]{/first@dp}
      {/dp}
      PAD,
      '[x,y,]' ],

    [ 'data with toData stores the set for later',
      <<<'PAD'
      {data 'dt'}
        ["x","y"]
      {/data}
      {dt}
        {first@dt}{data@dt toData='dcopy'/}{/first@dt}
      {/dt}
      [{data:dcopy}{$dt},{/data:dcopy}]
      PAD,
      '[x,y,]' ],

  ];

?>
