<?php

  $tagAbout   = 'Inside an overriding block: the content of the block it overrides.';
  $tagGroup   = 'layout';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{block 'name'} {parent} ... {/block}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'block', 'extends' ];

?>
