<?php

  $tagAbout   = 'Draws a score as stars or hearts in inline SVG - whole ones filled, a part of one clipped - and says it in words.';
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{rating 4.5}
{rating $score, max=5, size=20, icon='heart', label='text'}
PAD;

  $tagParms   = [
    'score' => 'The score, a number, clamped to 0 ... <code>max</code>.' ];

  $tagOptions = [
    'max'   => 'The number of icons, a whole number from 1 to 20, default 5.',
    'size'  => 'The height in pixels, default 20.',
    'icon'  => '<code>star</code> (default) or <code>heart</code>.',
    'label' => 'The accessible name, default <code>4.5 out of 5</code>.' ];

  $tagSee     = [ 'progress', 'icon', 'poll' ];

?>
