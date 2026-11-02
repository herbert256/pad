<?php

  $tagAbout   = 'Marks a part of a cached page or {pad:cache} section that renders again on every request, the hit included.';
  $tagGroup   = 'output';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:nocache} ... {/pad:nocache}
{pad:cache 'name', ttl=300} ... {pad:nocache} ... {/pad:nocache} ... {/pad:cache}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'cache', 'fragment', 'csrf', 'nonce' ];

?>
