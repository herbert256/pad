<?php

  $tagAbout   = 'Renders its content again and again until a condition becomes true.';
  $tagGroup   = 'loops';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{until condition} ... {/until}
{until} ... {/until condition}
PAD;

  $tagParms   = [
    'condition' => 'An expression, evaluated again before every pass; the loop ends when it becomes true. Written on the closing tag instead, it is tested after each pass, so the content renders at least once.' ];

  $tagOptions = [];

  $tagSee     = [ 'while', 'decrement', 'break', 'cease' ];

?>
