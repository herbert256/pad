<?php

  $tagAbout   = 'Rewrites the HTML it wraps as tidied, indented markup through PHP\'s tidy extension.';
  $tagGroup   = 'output';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{tidy} ... {/tidy}
PAD;

  $tagParms   = [];
  $tagOptions = [];

  $tagSee     = [ 'spaceless', 'output', 'flush' ];

?>
