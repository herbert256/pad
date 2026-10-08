<?php

  $tagAbout   = 'A built-in demo tag that answers a fixed line, there to show how the type prefixes resolve a name.';
  $tagGroup   = 'output';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{foo}
{pad:foo}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'pad', 'echo' ];

?>
