<?php

  // clap - a hand clap as a drum machine makes it: three bursts of noise a hundredth of a
  // second apart, the hands not meeting all at once, then the short ring of the room.

  return [
    'title'  => 'Clap',
    'level'  => 0.7,
    'voices' => [
      [ 'wave' => 'noise', 'notes' => 'x x x', 'step' => 0.011, 'length' => 0.011, 'attack' => 0.0005, 'decay' => 0.01, 'sustain' => 0, 'release' => 0.0005,
        'gain' => 0.8, 'filter' => [ 'bandpass', 1200, 1200, 1.4 ] ],
      [ 'wave' => 'noise', 'start' => 0.033, 'length' => 0.17, 'attack' => 0.001, 'decay' => 0.17, 'sustain' => 0,
        'gain' => 0.5, 'filter' => [ 'bandpass', 1100, 1100, 1.0 ] ],
    ],
  ];

?>
