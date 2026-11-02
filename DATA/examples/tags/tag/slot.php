<?php

  $tagAbout   = 'A named place in a custom tag\'s template - and the fill a caller gives it.';
  $tagGroup   = 'layout';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{pad:slot 'name'} default content {/pad:slot}
{pad:slot 'name'} <footer>@content@</footer> {/pad:slot}
{pad:slot 'name'/}
PAD;

  $tagParms   = [
    'name' => 'The slot\'s name, required.' ];

  $tagOptions = [];

  $tagSee     = [ 'parms', 'define', 'macro', 'block', 'shadow' ];

?>
