<?php

  // laser - the zap of a space game: a square wave diving from high to low.

  return [
    'title'  => 'Laser',
    'voices' => [
      [ 'wave' => 'square', 'note' => 1800, 'to' => 180, 'glide' => 0.22, 'length' => 0.24, 'attack' => 0.002, 'decay' => 0.24, 'sustain' => 0.2,
        'duty' => 0.3, 'gain' => 0.5, 'filter' => [ 'lowpass', 5000, 1500, 0.9 ] ],
    ],
  ];

?>
