<?php

  $tagAbout   = 'Sends the page rendered so far to the browser at once, so it can load the stylesheets while a slow part below renders.';
  $tagGroup   = 'output';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{flush}
PAD;

  $tagParms   = [];
  $tagOptions = [];

  $tagSee     = [ 'output', 'stack', 'cache' ];

?>
