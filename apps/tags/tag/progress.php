<?php

  $tagAbout   = 'Writes an accessible progress bar - the native progress element, styled, with its label and percentage - or a row of step dots.';
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{progress 72}
{progress $done, max=100, label='text', color='success', size='small'}
{progress 3, max=5, steps, label='text'}
PAD;

  $tagParms   = [
    'value' => 'The value, a number, clamped to 0 ... <code>max</code>.' ];

  $tagOptions = [
    'max'   => 'What the value counts to, default 100. With <code>steps</code> the number of steps, a whole number up to 50.',
    'label' => 'Shown above the bar and the accessible name; default <code>Progress</code> (not shown).',
    'color' => 'A colour slot: <code>accent</code> (default), <code>success</code>, <code>warning</code>, <code>danger</code> or <code>neutral</code>.',
    'size'  => '<code>small</code>, <code>medium</code> (default) or <code>large</code> - the height of the bar or the dots.',
    'steps' => 'Bare option: a dot per step joined by a line, the steps up to the value filled and the current one ringed.' ];

  $tagSee     = [ 'rating', 'countdown' ];

?>
