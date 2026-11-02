<?php

  $tagAbout   = 'Inside a {pad:tree}: renders its content only when the current row has children, its @else@ part for a leaf.';
  $tagGroup   = 'loops';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:branch} ... {pad:recurse/} ... {/pad:branch}
{pad:branch} ... @else@ ... {/pad:branch}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'tree', 'recurse' ];

?>
