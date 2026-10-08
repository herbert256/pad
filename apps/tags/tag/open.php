<?php

  $tagAbout   = 'Writes a literal opening brace that the template engine does not read as the start of a tag.';
  $tagGroup   = 'output';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{open}
PAD;

  $tagParms   = [];
  $tagOptions = [];

  $tagSee     = [ 'close', 'ignore' ];

?>
