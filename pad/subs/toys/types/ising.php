<?php

  // ising - a magnet made of tiny spins, the two-dimensional Ising model: on a square grid
  // every spin points up or down and would rather point as its four neighbours do, while the
  // heat flips spins at random. Cold, the spins line up into large domains and the grid is a
  // magnet; hot, they are noise; between, at the critical temperature 2 / ln(1 + √2) =
  // 2.269 that Onsager found exactly, domains of every size come and go. Each step is a
  // sweep - the Metropolis rule tried once a spin on average: a flip that lowers the energy
  // is taken, one that raises it by ΔE taken with the chance e^(-ΔE/T) - or clusters of like
  // spins set anew whole by Swendsen and Wang's rule, which keeps up near the critical point;
  // the edges wrap round. The magnetisation and the energy a spin are measured as it runs and
  // set beside Onsager's exact values for a grid without end, and a strip shows the
  // magnetisation going by. Drag to turn spins up, Shift or the right button down. temperature= in units of the coupling over
  // Boltzmann's constant (2.27 unless given, 0.5 to 5); field= an outside field pulling the
  // spins up, or down below 0 (0, -1 to 1); size= the spins across and down (128, 16 to
  // 256); algorithm= metropolis (unless given) or clusters; initial= random, a hot start (unless
  // given), or up, a cold one; speed= the sweeps a second (20, 1 to 100).
  //
  //   {ising temperature=2.0, initial='up'/}
  //   {ising algorithm='clusters', size=200, seed=4/}

  return padToy ( 'ising', 'Ising model', [
    'temperature' => padToyNumber ( 'temperature', 2.27, 0.5, 5 ),
    'field'       => padToyNumber ( 'field', 0, -1, 1 ),
    'size'        => padToyInt ( 'size', 128, 16, 256 ),
    'algorithm'   => padToyChoice ( 'algorithm', 'metropolis', [ 'metropolis', 'clusters' ] ),
    'initial'     => padToyChoice ( 'initial', 'random', [ 'random', 'up' ] ),
    'speed'       => padToyInt ( 'speed', 20, 1, 100 )
  ] );

?>
