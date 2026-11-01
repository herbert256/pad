<?php

  // waterdrop - a drop falling into water: a sine whose pitch leaps up as the bubble it traps
  // closes, ringing a moment at the top, and a smaller drop just after it.

  return [
    'title'  => 'Water drop',
    'level'  => 0.6,
    'voices' => [
      [ 'wave' => 'sine', 'note' => 750, 'to' => 2300, 'glide' => 0.04, 'length' => 0.15, 'attack' => 0.001, 'decay' => 0.15, 'sustain' => 0, 'gain' => 0.8 ],
      [ 'wave' => 'sine', 'note' => 1100, 'to' => 2900, 'glide' => 0.03, 'start' => 0.1, 'length' => 0.08, 'attack' => 0.002, 'decay' => 0.08, 'sustain' => 0, 'gain' => 0.22 ],
    ],
  ];

?>
