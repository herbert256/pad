<?php

  $tagAbout   = 'Marks a part of a cached page or {cache} section that renders again on every request, the hit included.';
  $tagGroup   = 'output';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{nocache} ... {/nocache}
{cache 'name', ttl=300} ... {nocache} ... {/nocache} ... {/cache}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'cache', 'fragment', 'csrf', 'nonce' ];

?>
