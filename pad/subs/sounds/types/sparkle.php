<?php

  // sparkle - a little magic: high bell tones of E major tumbling over each other, each ringing
  // on under the next, with a breath of bright noise - something unlocked, a star earned.

  return [
    'title'  => 'Sparkle',
    'level'  => 0.5,
    'voices' => [
      [ 'wave' => 'sine', 'notes' => 'E7 B6 G#7 E7 B7', 'step' => 0.07, 'length' => 0.3, 'attack' => 0.002, 'decay' => 0.3, 'sustain' => 0, 'gain' => 0.4 ],
      [ 'wave' => 'sine', 'notes' => 'C#7 F#7 C#8 G#7', 'step' => 0.07, 'start' => 0.035, 'length' => 0.22, 'attack' => 0.002, 'decay' => 0.22, 'sustain' => 0, 'gain' => 0.22 ],
      [ 'wave' => 'noise', 'length' => 0.45, 'attack' => 0.06, 'decay' => 0.39, 'sustain' => 0,
        'gain' => 0.05, 'filter' => [ 'highpass', 7000, 7000, 0.7 ] ],
    ],
  ];

?>
