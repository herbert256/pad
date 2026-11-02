<?php

  $tagAbout   = 'The browser\'s checker of the validation rules as one inline script, for a form a component draws.';
  $tagGroup   = 'forms';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{input:validator/}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'form', 'input', 'nonce' ];

  $subsystemKindLinks = appSubsystemLinks ( 'rule' );

?>
