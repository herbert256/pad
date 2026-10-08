<?php

  $tagAbout   = 'Renders its content when a user is logged in, with the user\'s row as its data, and its else half for a guest.';
  $tagGroup   = 'access';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{auth} ... {/auth}
{auth} Hello {$name} {else} ... {/auth}
{auth} ... @else@ ... {/auth}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'guest', 'can', 'cannot', 'feature' ];

?>
