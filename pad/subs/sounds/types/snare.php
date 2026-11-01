<?php

  // snare - a snare drum: a burst of noise, highpassed for the rattle of the snares, over a
  // short falling tone for the drum's body.

  return [
    'title'  => 'Snare',
    'voices' => [
      [ 'wave' => 'noise', 'length' => 0.22, 'attack' => 0.001, 'decay' => 0.21, 'sustain' => 0, 'release' => 0.01,
        'gain' => 0.7, 'filter' => [ 'highpass', 1400, 1400, 0.7 ] ],
      [ 'wave' => 'triangle', 'note' => 200, 'to' => 150, 'glide' => 0.08, 'length' => 0.12, 'attack' => 0.001, 'decay' => 0.12, 'sustain' => 0, 'gain' => 0.6 ],
    ],
  ];

?>
