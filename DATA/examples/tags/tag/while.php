<?php

  $tagAbout   = 'Renders its content again and again for as long as a condition holds.';
  $tagGroup   = 'loops';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{while condition} ... {/while}
{while} ... {/while condition}
PAD;

  $tagParms   = [
    'condition' => 'An expression, evaluated again before every pass; the loop ends when it is false. Written on the closing tag instead, it is tested after each pass, so the content renders at least once.' ];

  $tagOptions = [];

  $tagSee     = [ 'until', 'increment', 'break', 'continue', 'cease' ];

?>
