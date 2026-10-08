<?php

  $tagAbout   = 'Writes a literal closing brace that the template engine does not read as the end of a tag.';
  $tagGroup   = 'output';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{close}
PAD;

  $tagParms   = [];
  $tagOptions = [];

  $tagSee     = [ 'open', 'ignore' ];

?>
