<?php

  $tagAbout   = 'A part of a cached section or page that renders on every request, the cache hit included.';
  $tagGroup   = 'output';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{nocache} ... {/nocache}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'cache', 'push', 'csrf' ];

?>
