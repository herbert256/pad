<?php

  // notify - a gentle ding-dong, G then C above it: something came in. Sine bells with a
  // triangle an octave below for warmth.

  return [
    'title'  => 'Notify',
    'voices' => [
      [ 'wave' => 'sine',     'notes' => 'G5 C6', 'step' => 0.16, 'length' => 0.55, 'attack' => 0.005, 'decay' => 0.55, 'sustain' => 0, 'gain' => 0.5 ],
      [ 'wave' => 'triangle', 'notes' => 'G4 C5', 'step' => 0.16, 'length' => 0.45, 'attack' => 0.005, 'decay' => 0.45, 'sustain' => 0, 'gain' => 0.18 ],
    ],
  ];

?>
