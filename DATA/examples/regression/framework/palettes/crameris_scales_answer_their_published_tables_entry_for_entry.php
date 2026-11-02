<?php

  // Crameri's scales sampled in as many steps as their tables have entries answer the tables
  // themselves: batlow, vik and romaO in 256 steps - romaO, which is cyclic, once round its
  // circle, its last colour one step short of its first again. The first, the middle and the
  // last colour of each and an md5 of all of them, the expected values made from the .txt
  // tables of Scientific colour maps 8.0.1 (each channel times 255, rounded) apart from PAD.

  $tables = [];

  foreach ( [ 'batlow' => 256, 'vik' => 256, 'romao' => 256 ] as $name => $steps ) {
    $colors    = padPalette ( $name, $steps );
    $tables [] = [ 'name'  => $name,
                   'first' => $colors [0],
                   'mid'   => $colors [128],
                   'last'  => $colors [ $steps - 1 ],
                   'md5'   => md5 ( implode ( ' ', $colors ) ) ];
  }

?>
