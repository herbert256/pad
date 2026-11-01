<?php

  // warning - two tones taking turns, high and low, four times: look out. A square wave,
  // softened by a lowpass filter, like a small siren.

  return [
    'title'  => 'Warning',
    'level'  => 0.45,
    'voices' => [
      [ 'wave' => 'square', 'notes' => 'A5 E5 A5 E5', 'step' => 0.15, 'length' => 0.14, 'attack' => 0.004, 'release' => 0.03,
        'gain' => 0.5, 'filter' => [ 'lowpass', 2600, 2600, 0.9 ] ],
    ],
  ];

?>
