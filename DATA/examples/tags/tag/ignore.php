<?php

  $tagAbout   = 'Keeps PAD from reading the braces in its content as tags - for inline JavaScript, CSS, JSON and code samples.';
  $tagGroup   = 'output';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:ignore} ... {/pad:ignore}
PAD;

  $tagParms   = [];
  $tagOptions = [];

  $tagSee     = [ 'open', 'close', 'highlight', 'markdown' ];

?>
