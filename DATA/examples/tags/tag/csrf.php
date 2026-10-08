<?php

  $tagAbout   = 'The hidden form field holding the CSRF token of the visitor\'s session, or the bare token.';
  $tagGroup   = 'forms';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{csrf}
{csrf token}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'token' => 'Bare option: the token alone instead of the hidden field - for a script that sends it back in an <code>X-CSRF-Token</code> header.' ];

  $tagSee     = [ 'form', 'nonce' ];

?>
