<?php

  // hihat - a closed hi-hat: a very short hiss of noise, all its low end filtered away.

  return [
    'title'  => 'Hi-hat',
    'voices' => [
      [ 'wave' => 'noise', 'length' => 0.06, 'attack' => 0.0005, 'decay' => 0.058, 'sustain' => 0, 'release' => 0.002,
        'gain' => 0.8, 'filter' => [ 'highpass', 7000, 7000, 0.7 ] ],
      [ 'wave' => 'square', 'note' => 3300, 'length' => 0.04, 'attack' => 0.0005, 'decay' => 0.04, 'sustain' => 0, 'duty' => 0.5,
        'gain' => 0.12, 'filter' => [ 'highpass', 6000, 6000, 0.7 ] ],
    ],
  ];

?>
