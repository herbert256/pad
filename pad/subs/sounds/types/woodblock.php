<?php

  // woodblock - the hollow knock of a wood block: a short sine at the block's ringing pitch,
  // a second one at its higher, inharmonic mode, and a tap of filtered noise for the stick.

  return [
    'title'  => 'Wood block',
    'level'  => 0.7,
    'voices' => [
      [ 'wave' => 'sine', 'note' => 880, 'to' => 830, 'glide' => 0.03, 'length' => 0.11, 'attack' => 0.0005, 'decay' => 0.11, 'sustain' => 0, 'gain' => 0.7 ],
      [ 'wave' => 'sine', 'note' => 2390, 'length' => 0.04, 'attack' => 0.0005, 'decay' => 0.04, 'sustain' => 0, 'gain' => 0.22 ],
      [ 'wave' => 'noise', 'length' => 0.006, 'attack' => 0.0003, 'decay' => 0.0055, 'sustain' => 0, 'release' => 0.0002,
        'gain' => 0.35, 'filter' => [ 'bandpass', 1800, 1800, 1.8 ] ],
    ],
  ];

?>
