<?php

  // The toys are the files of pad/subs/games/toys/types/, as the engine reads them (padToyTypes).

  $tagAbout   = 'A small live simulation or visual toy in the browser - ' . count ( padToyTypes () ) . ' of them, from the Game of Life and a flock of birds to the Mandelbrot set and falling sand.';
  $tagGroup   = 'games';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{toy:name/}
{toy:life pattern='gun', wrap/}
{toy:mandelbrot iterations=500/}
{toy:boids birds=200/}
{toy:sorting race/}
{toy:life}
  .O.
  ..O
  OOO
{/toy:life}
{toy:lsystem}
  axiom: X
  X: F[+X]-X
  angle: 25
{/toy:lsystem}
PAD;

  $tagParms   = [
    'kind after colon' => 'The toy, a file of <code>pad/subs/games/toys/types/</code>: '
            . implode ( ', ', array_map ( fn ( $toy ) => "<code>$toy</code>", padToyTypes () ) )
            . '. The toy: prefix selects it - <code>{toy:life/}</code>.' ];

  $tagOptions = [
    'seed'       => 'A whole number: what the toy picks at random is the same on every load. Without it the browser picks.',
    'title'      => 'The name the toy is shown under, else its own.',
    'paused'     => 'Bare option: the toy waits for its Play button, as every toy does when the visitor asks for reduced motion.',
    'width'      => 'life (64), sand (160), reactiondiffusion (192): the cells across; <code>height</code> the cells down.',
    'size'       => 'ant (160), percolation (64), ising (128): the cells, sites or spins across and down.',
    'speed'      => 'How fast it runs: generations, steps, rows or sweeps a second (life, ant, automaton, sorting, reactiondiffusion, ising), or a level of 1 to 10 (boids, flowfield, orbits, epicycles, percolation; starfield 1 to 20).',
    'pattern'    => 'life: glider, spaceship, pulsar, gun, pentadecathlon, rpentomino, acorn, diehard, random or empty - or a pattern of your own between the tags; reactiondiffusion: the feed and kill rates of coral, mitosis, spots, mazes, worms, holes, waves or chaos.',
    'initial'    => 'automaton: single or random; reactiondiffusion: what the second chemical starts as - spots, center or noise; ising: random, a hot start, or up, a cold one.',
    'rule'       => 'life: <code>B3/S23</code> and its like; ant: the turns, <code>RL</code>, <code>LLRR</code> ...; automaton: Wolfram\'s number, 0 to 255.',
    'wrap'       => 'Bare option, life: the edges of the grid join.',
    'algorithm'  => 'sorting: bubble, cocktail, insertion, selection, gnome, shell, comb, quick, merge, heap or radix; <code>race</code> runs them all side by side; ising: metropolis, a spin at a time, or clusters, Swendsen and Wang\'s.',
    'order'      => 'sorting: what the bars start as - random, reversed, nearly or few; epicycles: the circles largest first, size, or slowest first, frequency.',
    'colour'     => 'kaleidoscope: rainbow, ink or palette; percolation: flow, the water in the accent, or clusters, each one a colour.',
    'feed'       => 'reactiondiffusion: the feed rate of the first chemical, 0 to 0.1 - a pattern\'s rates of your own, with <code>kill</code>.',
    'kill'       => 'reactiondiffusion: the rate the second chemical is taken away, 0 to 0.1.',
    'shape'      => 'epicycles: heart, star, square or trefoil - or the x,y points of a drawing of your own between the tags.',
    'circles'    => 'epicycles: how many circles turn (30, at most one fewer than the points).',
    'points'     => 'epicycles: the samples taken along the drawing (256, 16 to 1024).',
    'probability' => 'percolation: the chance a site is open (0.59, 0 to 1).',
    'neighbours' => 'percolation: 4, the sites beside, or 8, the corners too.',
    'mode'       => 'percolation: what Play does - flow, the water seeping down, or experiment, trials that estimate the threshold.',
    'temperature' => 'ising: the heat, 0.5 to 5 (2.27, near the critical point).',
    'field'      => 'ising: an outside field pulling the spins up, or down below 0 (0, -1 to 1).',
    'iterations' => 'mandelbrot: how long a point is followed (300); lsystem: how often the word is rewritten.',
    'palette'    => 'mandelbrot: ocean, fire, rainbow or mono.',
    'birds'      => 'boids: how many (150); <code>separation</code>, <code>alignment</code> and <code>cohesion</code> weigh the three rules, 0 to 10.',
    'pendulums'  => 'pendulum: how many swing side by side, each started a hundredth of a degree apart (1); <code>angles</code> the start.',
    'system'     => 'orbits: solar, binary, eight or swarm. An lsystem names its system as its first value: plant, dragon, koch, hilbert ...',
    'setup'      => 'ripples: two, slits or pond.',
    'show'       => 'clocks: analog, binary and words - which of them; <code>zone</code> a time zone.',
    'mirrors'    => 'kaleidoscope: how many wedges (8).',
    'levels'     => 'galton: the rows of pegs (12); <code>balls</code> how many, <code>bias</code> the chance of going right in percent.' ];

  $tagSee     = [ 'game:memory', 'chart:bar', 'diagram:auto' ];

?>
