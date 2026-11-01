<?php

  // bell - a struck bell that rings for two seconds: sine partials at the bell's inharmonic
  // ratios - the hum below, the strike note, a minor third, a fifth, the octave and above -
  // each dying away at its own pace.

  return [
    'title'  => 'Bell',
    'voices' => [
      [ 'wave' => 'sine', 'note' => 330,  'length' => 2.4, 'attack' => 0.003, 'decay' => 2.4, 'sustain' => 0, 'gain' => 0.25 ],
      [ 'wave' => 'sine', 'note' => 660,  'length' => 2.2, 'attack' => 0.002, 'decay' => 2.2, 'sustain' => 0, 'gain' => 0.45 ],
      [ 'wave' => 'sine', 'note' => 781,  'length' => 1.6, 'attack' => 0.002, 'decay' => 1.6, 'sustain' => 0, 'gain' => 0.22 ],
      [ 'wave' => 'sine', 'note' => 994,  'length' => 1.3, 'attack' => 0.002, 'decay' => 1.3, 'sustain' => 0, 'gain' => 0.18 ],
      [ 'wave' => 'sine', 'note' => 1320, 'length' => 1.0, 'attack' => 0.002, 'decay' => 1.0, 'sustain' => 0, 'gain' => 0.16 ],
      [ 'wave' => 'sine', 'note' => 1822, 'length' => 0.6, 'attack' => 0.001, 'decay' => 0.6, 'sustain' => 0, 'gain' => 0.1  ],
      [ 'wave' => 'sine', 'note' => 2640, 'length' => 0.3, 'attack' => 0.001, 'decay' => 0.3, 'sustain' => 0, 'gain' => 0.06 ],
    ],
  ];

?>
