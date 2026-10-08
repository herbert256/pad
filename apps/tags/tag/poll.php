<?php

  $tagAbout   = 'A vote among a few answers, once per visitor, then the results as bars with percentages.';
  $tagGroup   = 'widgets';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{poll 'name', options='Yes, No'}
{poll 'name', options='PHP, Python, Go', title='Question?', button='Vote', results}
PAD;

  $tagParms   = [
    'name' => 'Letters, digits, <code>_</code> and <code>-</code>, at most 64: names the file the votes are kept in, <code>DATA/poll/&lt;app&gt;/&lt;name&gt;.json</code>.' ];

  $tagOptions = [
    'options' => 'The answers, separated by commas - two or more; duplicates and empty ones are dropped.',
    'title'   => 'The question. The name made readable when not given (<code>favorite-language</code> is <code>Favorite language</code>).',
    'button'  => 'The text of the vote button, <code>Vote</code> when not given.',
    'results' => 'Bare option: show the results to everyone, also before a vote.' ];

  $tagSee     = [ 'live', 'form', 'rating', 'progress' ];

?>
