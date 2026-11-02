<?php

  $tagAbout   = 'This request\'s Content-Security-Policy nonce, for a script or style the policy lets run.';
  $tagGroup   = 'forms';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{pad:nonce/}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'csrf', 'asset', 'validator' ];

?>
