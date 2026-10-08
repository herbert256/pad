<?php

  $tagAbout   = 'Inside a {tree}: renders its content only when the current row has children, its @else@ part for a leaf.';
  $tagGroup   = 'loops';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{branch} ... {recurse} ... {/branch}
{branch} ... @else@ ... {/branch}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'tree', 'recurse' ];

?>
