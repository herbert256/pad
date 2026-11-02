<?php

  $tagAbout   = 'Takes out the whitespace between HTML tags, and at both ends of the content.';
  $tagGroup   = 'output';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:spaceless} ... {/pad:spaceless}
PAD;

  $tagParms   = [];
  $tagOptions = [];

  $tagSee     = [ 'tidy', 'ignore' ];

?>
