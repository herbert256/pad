<?php

  $tagAbout   = 'The constant FALSE: its content is skipped and its @else@ part renders instead.';
  $tagGroup   = 'conditions';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{pad:false/}
{pad:false} ... {/pad:false}
{pad:false} ... @else@ ... {/pad:false}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'true', 'null', 'if' ];

?>
