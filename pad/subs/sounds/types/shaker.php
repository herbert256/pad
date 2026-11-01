<?php

  // shaker - a shaker moved forth and back: two soft swells of bright noise, the second a
  // little weaker, each rising as the beads roll and stopping as they hit the end.

  return [
    'title'  => 'Shaker',
    'level'  => 0.55,
    'voices' => [
      [ 'wave' => 'noise', 'length' => 0.1, 'attack' => 0.045, 'decay' => 0.05, 'sustain' => 0, 'release' => 0.005,
        'gain' => 0.6, 'filter' => [ 'bandpass', 5200, 6200, 1.1 ] ],
      [ 'wave' => 'noise', 'start' => 0.16, 'length' => 0.09, 'attack' => 0.04, 'decay' => 0.045, 'sustain' => 0, 'release' => 0.005,
        'gain' => 0.42, 'filter' => [ 'bandpass', 5800, 4800, 1.1 ] ],
    ],
  ];

?>
