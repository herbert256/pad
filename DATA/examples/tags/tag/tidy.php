<?php

  $tagAbout   = 'Rewrites the HTML it wraps as tidied, indented markup through PHP\'s tidy extension.';
  $tagGroup   = 'output';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:tidy} ... {/pad:tidy}
PAD;

  $tagParms   = [];
  $tagOptions = [];

  $tagSee     = [ 'spaceless', 'output', 'flush' ];

?>
