<?php

  $tagAbout   = 'Answers the next value of a rotating list on every call - striped rows without a counter.';
  $tagGroup   = 'conditions';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{switch 'odd', 'even'}
{switch 'red', 'green', 'blue'}
PAD;

  $tagParms   = [
    'values' => 'Two or more values to cycle through; the first call answers the first, then each call the next, starting over after the last.' ];

  $tagOptions = [];

  $tagSee     = [ 'case', 'ifchanged' ];

?>
