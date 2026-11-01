<?php

  // applause - a short round of applause from a small crowd that is made up: five pairs of
  // hands, each clapping at its own pace and pitch with a pause here and there, some stopping
  // early, over the hiss of the room - dense at once, thinning out as it fades.

  return [
    'title'  => 'Applause',
    'voices' => [
      [ 'wave' => 'noise', 'notes' => 'x x x x - x x x x x - x x x x x x', 'step' => 0.091, 'length' => 0.035, 'attack' => 0.0008, 'decay' => 0.033, 'sustain' => 0, 'release' => 0.002,
        'gain' => 0.5, 'filter' => [ 'bandpass', 1300, 1300, 1.3 ] ],
      [ 'wave' => 'noise', 'notes' => 'x x - x x x x - x x x x x', 'step' => 0.107, 'start' => 0.023, 'length' => 0.035, 'attack' => 0.0008, 'decay' => 0.033, 'sustain' => 0, 'release' => 0.002,
        'gain' => 0.5, 'filter' => [ 'bandpass', 1800, 1800, 1.3 ] ],
      [ 'wave' => 'noise', 'notes' => 'x x x x x - x x x x', 'step' => 0.119, 'start' => 0.047, 'length' => 0.035, 'attack' => 0.0008, 'decay' => 0.033, 'sustain' => 0, 'release' => 0.002,
        'gain' => 0.45, 'filter' => [ 'bandpass', 2400, 2400, 1.3 ] ],
      [ 'wave' => 'noise', 'notes' => 'x x x - x x x x x', 'step' => 0.083, 'start' => 0.068, 'length' => 0.035, 'attack' => 0.0008, 'decay' => 0.033, 'sustain' => 0, 'release' => 0.002,
        'gain' => 0.45, 'filter' => [ 'bandpass', 950, 950, 1.3 ] ],
      [ 'wave' => 'noise', 'notes' => 'x x x x x x', 'step' => 0.133, 'start' => 0.012, 'length' => 0.035, 'attack' => 0.0008, 'decay' => 0.033, 'sustain' => 0, 'release' => 0.002,
        'gain' => 0.4, 'filter' => [ 'bandpass', 3000, 3000, 1.3 ] ],
      [ 'wave' => 'noise', 'length' => 1.5, 'attack' => 0.08, 'decay' => 1.42, 'sustain' => 0,
        'gain' => 0.16, 'filter' => [ 'bandpass', 2000, 2000, 0.6 ] ],
    ],
  ];

?>
