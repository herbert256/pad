<?php

  $tagAbout   = 'Renders its content when nobody is logged in, and its else half for a user.';
  $tagGroup   = 'access';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:guest} ... {/pad:guest}
{pad:guest} ... {pad:else} ... {/pad:guest}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'auth', 'can' ];

?>
