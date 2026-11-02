<?php

  // TOML written: plain values first, then a [table] per table - left out for one that holds
  // tables only - and an [[array]] per table of a list of tables, an empty one among them; a
  // text that is a date written bare, a float with its .0, inf and nan, a key that is no bare
  // key quoted. Read again it is the value it was written from.

  $tomlValue = [
    'title'   => 'Weekend "away"',
    'start'   => '2026-11-06',
    'at'      => '2026-11-06T09:30:00Z',
    'ratio'   => 2.0,
    'limit'   => INF,
    'tags'    => [ 'a', 'b' ],
    'point'   => [ 'x' => 1, 'y' => [ 2, 3 ] ],
    'a b'     => 'spaced key',
    'servers' => [ 'alpha' => [ 'ip' => '10.0.0.1' ] ],
    'stops'   => [ [ 'name' => 'Gravensteen', 'hours' => 2 ], [], [ 'name' => 'Graslei', 'hours' => 1.5 ] ]
  ];

  $tomlText = padFormatWrite ( 'toml', $tomlValue );
  $tomlBack = padFormatRead  ( 'toml', $tomlText );

  $tomlSame = ( $tomlBack == $tomlValue ) ? 'the same' : 'another value: ' . json_encode ( $tomlBack );
  $tomlShow = str_replace ( "\n", ' | ', trim ( $tomlText ) );

?>
