<?php

  $tagAbout   = 'Renders its content when a user is logged in, with the user\'s row as its data, and its else half for a guest.';
  $tagGroup   = 'access';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:auth} ... {/pad:auth}
{pad:auth} Hello {$name} {pad:else} ... {/pad:auth}
{pad:auth} ... @else@ ... {/pad:auth}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'guest', 'can', 'cannot', 'feature' ];

?>
