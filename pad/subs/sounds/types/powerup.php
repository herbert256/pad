<?php

  // powerup - collected something that makes you stronger: a fast run of square notes up
  // two octaves, each bending up as it goes, with a wobble on top.

  return [
    'title'  => 'Power-up',
    'voices' => [
      [ 'wave' => 'square', 'notes' => 'C5 E5 G5 C6 E6 G6 C7', 'step' => 0.055, 'length' => 0.07, 'bend' => 2, 'attack' => 0.002, 'release' => 0.01,
        'duty' => 0.25, 'gain' => 0.45, 'vibrato' => [ 18, 0.3 ], 'filter' => [ 'lowpass', 6000, 6000, 0.7 ] ],
      [ 'wave' => 'triangle', 'note' => 'C7', 'start' => 0.385, 'length' => 0.3, 'attack' => 0.002, 'decay' => 0.3, 'sustain' => 0,
        'vibrato' => [ 9, 0.4 ], 'gain' => 0.35 ],
    ],
  ];

?>
