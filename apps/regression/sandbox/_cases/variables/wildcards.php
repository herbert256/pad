<?php

  // The wildcard forms of a dotted reference: * for any element, < for the first, > for the
  // last. The data is a cube where every value spells its own path - $a.b.2.c.3.1 is 231 - so
  // a wrong turn anywhere shows up as a wrong digit.
  //
  // * picks at random, so those cases are pinned by shape: the digits the path fixes have to
  // be right and the rest may be anything. That is exactly what check/vars/at/random.pad was
  // written to work out, and it sat behind an {exit} for so long that none of it ran. The
  // reason it could not run: padAtSearchGo kept a name's position in the path and the array
  // key it resolves to in one variable, and padAtSearchAny reads that argument as a position
  // to see what is left of the path after the *. It was handed the key instead, so every *
  // either walked into a scalar - ending the request on foreach() - or came back not found.

  $cube = [];

  foreach ( [ 1, 2, 3 ] as $i )
    foreach ( [ 1, 2, 3 ] as $j )
      foreach ( [ 1, 2, 3 ] as $k )
        $cube ['b'] [$i] ['c'] [$j] [$k] = (int) "$i$j$k";

  return [

    [ 'an exact path',
      <<<'PAD'
      {$a.b.1.c.1.1}
      PAD,
      '111',
      [ 'a' => $cube ] ],

    [ 'an exact path at the far corner',
      <<<'PAD'
      {$a.b.3.c.3.3}
      PAD,
      '333',
      [ 'a' => $cube ] ],

    [ '< takes the first',
      <<<'PAD'
      {$a.b.<.c.1.1}
      PAD,
      '111',
      [ 'a' => $cube ] ],

    [ '> takes the last',
      <<<'PAD'
      {$a.b.>.c.1.1}
      PAD,
      '311',
      [ 'a' => $cube ] ],

    [ '* on a flat list gives one of its values',
      <<<'PAD'
      {$flat.*}
      PAD,
      '/^(one|two|three)$/',
      [ 'flat' => [ 1 => 'one', 2 => 'two', 3 => 'three' ] ] ],

    [ '* as the last name, with the rest of the path fixed',
      <<<'PAD'
      {$a.b.1.c.1.*}
      PAD,
      '/^11[123]$/',
      [ 'a' => $cube ] ],

    [ '< twice, then * for the last',
      <<<'PAD'
      {$a.b.<.c.<.*}
      PAD,
      '/^11[123]$/',
      [ 'a' => $cube ] ],

    [ '> twice, then * for the last',
      <<<'PAD'
      {$a.b.>.c.>.*}
      PAD,
      '/^33[123]$/',
      [ 'a' => $cube ] ],

    [ '* in the middle, < at the end',
      <<<'PAD'
      {$a.b.*.c.*.<}
      PAD,
      '/^[123][123]1$/',
      [ 'a' => $cube ] ],

    [ '* in the middle, > at the end',
      <<<'PAD'
      {$a.b.*.c.*.>}
      PAD,
      '/^[123][123]3$/',
      [ 'a' => $cube ] ],

    [ '* the whole way down',
      <<<'PAD'
      {$a.b.*.c.*.*}
      PAD,
      '/^[123][123][123]$/',
      [ 'a' => $cube ] ],

  ];

?>