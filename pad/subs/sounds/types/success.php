<?php

  // success - a quick rising major arpeggio, C E G C: done, and done right. A triangle wave
  // doubled by a sine an octave below.

  return [
    'title'  => 'Success',
    'voices' => [
      [ 'wave' => 'triangle', 'notes' => 'C5 E5 G5 C6', 'step' => 0.08, 'length' => 0.22, 'attack' => 0.004, 'decay' => 0.2, 'sustain' => 0.15, 'release' => 0.04, 'gain' => 0.45 ],
      [ 'wave' => 'sine',     'notes' => 'C4 E4 G4 C5', 'step' => 0.08, 'length' => 0.22, 'attack' => 0.004, 'decay' => 0.2, 'sustain' => 0.15, 'release' => 0.04, 'gain' => 0.3  ],
    ],
  ];

?>
