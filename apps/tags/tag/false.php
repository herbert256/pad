<?php

  $tagAbout   = 'The constant FALSE: its content is skipped and its @else@ part renders instead.';
  $tagGroup   = 'conditions';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{false}
{false} ... {/false}
{false} ... @else@ ... {/false}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'true', 'null', 'if' ];

?>
