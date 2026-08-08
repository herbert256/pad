<?php

  // The keys property: the target level's data set turned into iterable name/value rows,
  // one per key. Guarded by first@ because the property answers about the whole set, the
  // same on every occurrence.

  return [

    [ 'keys iterates an occurrence as name and value',
      <<<'PAD'
      {data 'kv' ignore}
        {"a":1,"b":2}
      {/data}
      {kv}
        {first@kv}{keys@kv}[{$name}]{/keys@kv}{/first@kv}
      {/kv}
      PAD,
      '[a][b]' ],

  ];

?>
