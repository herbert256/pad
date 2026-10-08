<?php

  $tagAbout   = 'Prints everything pushed to a named stack, from above or below it in the page.';
  $tagGroup   = 'layout';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{stack 'name'}
PAD;

  $tagParms   = [
    'name' => 'The name of the stack, required.' ];

  $tagOptions = [];

  $tagSee     = [ 'push', 'toc', 'cache' ];

?>
