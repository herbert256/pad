<?php

  // The toys are the files of pad/toys/types/, as the engine reads them (padToyTypes).

  $tagAbout   = 'A small live simulation or visual toy in the browser - ' . count ( padToyTypes () ) . ' of them, from the Game of Life and a flock of birds to the Mandelbrot set and falling sand.';
  $tagGroup   = 'games';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{toy 'name'/}
{toy 'life', pattern='gun', wrap/}
{toy:mandelbrot iterations=500/}
{boids birds=200/}
{sorting race/}
{life} .O. / ..O / OOO {/life}
{lsystem} axiom: X / X: F[+X]-X / angle: 25 {/lsystem}
PAD;

  $tagParms   = [
    'name' => 'The toy, a file of <code>pad/toys/types/</code>: '
            . implode ( ', ', array_map ( fn ( $toy ) => "<code>$toy</code>", padToyTypes () ) )
            . '. The toy: type names it as well - <code>{toy:life/}</code>, or bare, <code>{life/}</code>, when nothing else has the name.' ];

  $tagOptions = [
    'seed'       => 'A whole number: what the toy picks at random is the same on every load. Without it the browser picks.',
    'title'      => 'The name the toy is shown under, else its own.',
    'paused'     => 'Bare option: the toy waits for its Play button, as every toy does when the visitor asks for reduced motion.',
    'width'      => 'life (64), sand (160): the cells across; <code>height</code> the cells down.',
    'speed'      => 'How fast it runs: generations, steps or rows a second (life, ant, automaton, sorting), or a level of 1 to 10 (boids, starfield, flowfield, orbits).',
    'pattern'    => 'life: glider, spaceship, pulsar, gun, pentadecathlon, rpentomino, acorn, diehard, random or empty - or a pattern of your own between the tags.',
    'rule'       => 'life: <code>B3/S23</code> and its like; ant: the turns, <code>RL</code>, <code>LLRR</code> ...; automaton: Wolfram\'s number, 0 to 255.',
    'wrap'       => 'Bare option, life: the edges of the grid join.',
    'algorithm'  => 'sorting: bubble, cocktail, insertion, selection, gnome, shell, comb, quick, merge, heap or radix; <code>race</code> runs them all side by side.',
    'iterations' => 'mandelbrot: how long a point is followed (300); lsystem: how often the word is rewritten.',
    'palette'    => 'mandelbrot: ocean, fire, rainbow or mono.',
    'birds'      => 'boids: how many (150); <code>separation</code>, <code>alignment</code> and <code>cohesion</code> weigh the three rules, 0 to 10.',
    'pendulums'  => 'pendulum: how many swing side by side, each started a hundredth of a degree apart (1); <code>angles</code> the start.',
    'system'     => 'orbits: solar, binary, eight or swarm. An lsystem names its system as its first value: plant, dragon, koch, hilbert ...',
    'setup'      => 'ripples: two, slits or pond.',
    'show'       => 'clocks: analog, binary and words - which of them; <code>zone</code> a time zone.',
    'mirrors'    => 'kaleidoscope: how many wedges (8).',
    'levels'     => 'galton: the rows of pegs (12); <code>balls</code> how many, <code>bias</code> the chance of going right in percent.' ];

  $tagSee     = [ 'game', 'chart', 'diagram' ];

?>
