<?php

  $tagAbout   = 'The constant NULL: its content is dropped, the @else@ part too, and a null= option fires.';
  $tagGroup   = 'conditions';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{pad:null/}
{pad:null} ... {/pad:null}
{pad:null null='name'} ... {/pad:null}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'true', 'false', 'content' ];

?>
