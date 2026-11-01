<?php

  // The effects are the files of pad/subs/effects/types/, as the engine reads them (padKinds).

  $tagAbout   = 'Motion and decoration on a text or a block, CSS first - ' . count ( padKinds ( 'effects' ) ) . ' of them, from a typewriter, a glitch and a marker stroke to a scroll reveal, a marquee, loaders and confetti.';
  $tagGroup   = 'widgets';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{effect 'name', ...} ... {/effect}
{effect:name ...} ... {/effect:name}
{typewriter speed=14, delay=0, caret='bar', mono} ... {/typewriter}
{glitch repeat=3, duration=2.5} ... {/glitch}
{shimmer} ... {/shimmer}
{gradient colors='#f97316, #db2777', angle=90, animate} ... {/gradient}
{wave height=0.25} ... {/wave}
{highlighter color='gold', scroll} ... {/highlighter}
{countup to=1250, from=0, prefix='€', suffix='+', scroll/}
{marquee duration=20, direction='left', gap=48, label='name'} ... {/marquee}
{reveal motion='up', distance=32} ... {/reveal}
{tilt angle=8, toward='top-right', glare} ... {/tilt}
{spotlight at='top', color='#f59e0b'} ... {/spotlight}
{spinner style='ring', label='Loading', size=24/}
{confetti pieces=120, spread=70, seed=7/}
PAD;

  $tagParms   = [
    'name'  => 'The effect, a file of <code>pad/subs/effects/types/</code>: '
             . implode ( ', ', array_map ( fn ( $effect ) => "<code>$effect</code>", padKinds ( 'effects' ) ) )
             . '. The effect: type names it as well - <code>{effect:typewriter}...{/effect:typewriter}</code> - and so does the bare name, <code>{typewriter}...{/typewriter}</code>, after every other kind of tag: a store, a field or an array of the name wins.',
    'first' => 'The effect\'s own first parameter, the second of <code>{effect}</code>: the text of a single tag - <code>{typewriter \'Hello\'/}</code> - or the number of a count-up, <code>{countup 1250/}</code>.' ];

  $tagOptions = [
    'duration'  => 'Seconds one run takes: a glitch\'s cycle, a shimmer\'s sweep, a marquee\'s round, a count.',
    'delay'     => 'Seconds before it starts.',
    'repeat'    => 'How often an animation that repeats runs - 3 unless given, <code>forever</code> as the author\'s choice.',
    'speed'     => 'typewriter: letters a second (14).',
    'caret'     => 'typewriter: <code>bar</code> or <code>none</code>.',
    'mono'      => 'Bare option, typewriter: monospaced letters, one step a letter exactly.',
    'colors'    => 'gradient, confetti: CSS colours between commas.',
    'color'     => 'highlighter: the ink; spotlight: the light; spinner: the spinner.',
    'angle'     => 'gradient: the direction in degrees (90); tilt: how far it leans (8).',
    'animate'   => 'Bare option, gradient: the colours flow along the letters.',
    'height'    => 'wave: how far a letter rises, in em (0.25).',
    'from'      => 'countup: the number it starts at (0); <code>to</code> the one it ends at.',
    'prefix'    => 'countup: a text before the number, <code>suffix</code> one after it.',
    'scroll'    => 'Bare option, countup and highlighter: counted or drawn as it scrolls into view.',
    'direction' => 'marquee: <code>left</code> or <code>right</code>.',
    'gap'       => 'marquee: pixels between one round and the next (48).',
    'label'     => 'marquee: the name of the band for a screen reader; spinner: what it says (<code>Loading</code>).',
    'motion'    => 'reveal: up, down, left, right, fade or zoom; <code>distance</code> how far in pixels (32).',
    'toward'    => 'tilt: the corner that comes toward the viewer - top-right, top-left, bottom-right, bottom-left.',
    'glare'     => 'Bare option, tilt: a sheen slides over the card as it leans.',
    'at'        => 'spotlight: where the light rests - top, center, a corner, or two percentages.',
    'style'     => 'spinner: ring, dots, bars, pulse or orbit.',
    'size'      => 'spinner: pixels (24).',
    'pieces'    => 'confetti: how many (120); <code>spread</code> the angle they fly out in; <code>seed</code> which pieces.' ];

  $tagSee     = [ 'widget', 'toy', 'section' ];

?>
