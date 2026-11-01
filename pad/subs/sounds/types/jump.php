<?php

  // jump - the hop of a platform game: a thin square pulse swept up an octave and more.

  return [
    'title'  => 'Jump',
    'voices' => [
      [ 'wave' => 'square', 'note' => 230, 'to' => 720, 'glide' => 0.16, 'length' => 0.2, 'attack' => 0.002, 'decay' => 0.18, 'sustain' => 0.3,
        'duty' => 0.25, 'gain' => 0.5, 'filter' => [ 'lowpass', 3500, 3500, 0.8 ] ],
    ],
  ];

?>
