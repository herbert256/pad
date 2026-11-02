<?php

  $tagAbout   = 'A tag with no behaviour of its own: its content renders once, or per row with data=, carrying the generic options.';
  $tagGroup   = 'pages';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:pad} ... {/pad:pad}
{pad:pad data='store', name='rows'} ... {/pad:pad}
{pad:pad $name = 'value'} ... {/pad:pad}
{pad:pad} ... {/pad:pad | upper}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'format:auto', 'content', 'code', 'set' ];

?>
