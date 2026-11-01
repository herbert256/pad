<?php

  // kick - a bass drum: a sine that drops from 150 Hz to 45 in a tenth of a second, with a
  // little click of noise on the beat.

  return [
    'title'  => 'Kick',
    'voices' => [
      [ 'wave' => 'sine', 'note' => 150, 'to' => 45, 'glide' => 0.12, 'length' => 0.45, 'attack' => 0.001, 'decay' => 0.45, 'sustain' => 0, 'gain' => 1.0 ],
      [ 'wave' => 'noise', 'length' => 0.012, 'attack' => 0.0005, 'decay' => 0.011, 'sustain' => 0, 'release' => 0.001,
        'gain' => 0.25, 'filter' => [ 'highpass', 2500, 2500, 0.7 ] ],
    ],
  ];

?>
