<?php

  // The patterns are the files of pad/subs/patterns/types/, as the engine reads them (padPatternKinds).

  $tagAbout   = 'Draws a seeded SVG background or a piece of generative art on the server - ' . count ( padPatternKinds () ) . ' kinds, from stripes and dots to contour lines and circle packing - the same bytes for the same options.';
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{pattern 'name'/}
{pattern 'topography', seed=7, width=960, height=320/}
{pattern 'dots', size=24, colors='#fdfbf7, #e0c9a6'/}
{pattern:voronoi seed=3, title='Shards of colour'/}
<div style="background-image:{pattern 'waves', as='url'/}">...</div>
PAD;

  $tagParms   = [
    'name' => 'The pattern, a file of <code>pad/subs/patterns/types/</code>: '
            . implode ( ', ', array_map ( fn ( $kind ) => "<code>$kind</code>", padPatternKinds () ) )
            . '. The pattern: type names it as well - <code>{pattern:topography/}</code>; a pattern is never reached bare.' ];

  $tagOptions = [
    'seed'    => 'A number or a word: what the pattern picks at random, the same for the same seed (1 when left out). A tile repeated - stripes, dots, grid, checks, chevron, isometric, herringbone, a brick wall of one ink - does not change with it.',
    'colors'  => 'A list of colours, the background first and the inks after it: <code>colors=\'#fff, #1d3557, #e63946\'</code> - hex, a name, or <code>rgb()</code>, <code>hsl()</code> and their like; <code>none</code> leaves the background out. Each kind has its own palette.',
    'size'    => 'The size of the motif in pixels - a stripe, a dot\'s tile, a hexagon\'s radius, a hill of the topography, the largest circle; each kind its own default and bounds.',
    'width'   => 'The width of the picture in pixels (480); the SVG covers its box when it is made larger or smaller, <code>preserveAspectRatio="xMidYMid slice"</code>.',
    'height'  => 'The height in pixels (240).',
    'opacity' => 'From 0 to 1: how strongly the motif shows over the background (1; grain 0.5).',
    'as'      => '<code>svg</code>, the default, writes the SVG; <code>url</code> writes <code>url(data:image/svg+xml,...)</code> for a style attribute - in either quotes - or a style sheet.',
    'title'   => 'The accessible name: the pattern becomes <code>role="img"</code> with <code>aria-label</code> and <code>&lt;title&gt;</code>. Without it the pattern is decoration, <code>aria-hidden</code>.',
    'angle'   => 'stripes and herringbone: the angle in degrees (45).',
    'levels'  => 'topography: the number of contour lines (12), every fifth the bolder index line.',
    'ratio'   => 'herringbone: a block\'s length to its width, a whole number (3); brick: a brick\'s length to its height (3).',
    'offset'  => 'brick: how far a course is shifted along the one below, in bricks - 0.5 the running bond, 0 stacked, a third or a quarter raking; taken in twelfths.',
    'joint'   => 'brick: the width of the mortar between the bricks in pixels (2), at most nearly half a brick\'s height.' ];

  $tagSee     = [ 'placeholder', 'identicon', 'chart' ];

?>
