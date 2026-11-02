<?php

  // reactiondiffusion - two chemicals that react as they spread, the Gray-Scott model: U is
  // fed in everywhere, V turns U into more V and is taken away, and from that alone grow
  // spots that split like cells, coral, mazes, worms and travelling waves - the patterns
  // Turing foresaw on the coats of animals. It is Pearson's model of 1993 on a grid that
  // wraps round at its edges: the five-point Laplacian, Du and Dv as his 2e-5 and 1e-5 over a
  // square of 2.5 split into 256 steps, a time step of 1 - a model to explore, not a
  // calibrated chemistry. Paint V with the pointer, Shift or the right button wipes it, the
  // arrow keys and Enter drop it too; the map picks a feed and kill of your own. pattern= the
  // feed and kill rates of a named pattern - coral unless given, mitosis, spots, mazes,
  // worms, holes, waves, chaos; feed= and kill= set them yourself (0 to 0.1); initial= what V
  // starts as - spots (unless given), center or noise; width= and height= the grid (192 by
  // 128, 64 to 256); speed= the steps a second (1200, 60 to 3000).
  //
  //   {toy:reactiondiffusion pattern='mitosis', initial='center'/}
  //   {toy:reactiondiffusion feed=0.03, kill=0.06, seed=5/}

  // The patterns to pick from: the feed rate F and the kill rate k, on Pearson's scale. The
  // rates come from the regions of Munafo's map of the model (mrob.com/pub/comp/xmorphia,
  // which names them by Pearson's Greek letters), each one checked to grow what it is named
  // for from the start of spots under the step this toy takes: coral spreading as branching
  // stripes, mitosis spots that split until they fill the grid, spots that keep still, mazes,
  // worms that lengthen without breaking, holes in a sea of V, waves - fragments of wave
  // fronts travelling - and chaos, never settling.

  $padRdPatterns = [
    'coral'   => [ 0.0545, 0.062  ],
    'mitosis' => [ 0.0367, 0.0649 ],
    'spots'   => [ 0.035,  0.066  ],
    'mazes'   => [ 0.029,  0.057  ],
    'worms'   => [ 0.05,   0.065  ],
    'holes'   => [ 0.039,  0.058  ],
    'waves'   => [ 0.01,   0.047  ],
    'chaos'   => [ 0.026,  0.051  ]
  ];

  $padRdPattern = padToyChoice ( 'pattern', 'coral', array_keys ( $padRdPatterns ) );

  return padToy ( 'reactiondiffusion', 'Reaction-diffusion', [
    'pattern'  => $padRdPattern,
    'feed'     => padToyNumber ( 'feed', $padRdPatterns [$padRdPattern] [0], 0, 0.1 ),
    'kill'     => padToyNumber ( 'kill', $padRdPatterns [$padRdPattern] [1], 0, 0.1 ),
    'initial'  => padToyChoice ( 'initial', 'spots', [ 'spots', 'center', 'noise' ] ),
    'width'    => padToyInt ( 'width', 192, 64, 256 ),
    'height'   => padToyInt ( 'height', 128, 64, 256 ),
    'speed'    => padToyInt ( 'speed', 1200, 60, 3000 ),
    'patterns' => $padRdPatterns
  ] );

?>
