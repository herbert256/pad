<?php

  $tagAbout   = 'The constant NULL: its content is dropped, the @else@ part too, and a null= option fires.';
  $tagGroup   = 'conditions';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{null}
{null} ... {/null}
{null null='name'} ... {/null}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'true', 'false', 'content' ];

?>
