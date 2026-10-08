<?php

  $tagAbout   = 'Takes one off a variable, starting it at -1 when it does not exist yet.';
  $tagGroup   = 'values';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{decrement $name}
PAD;

  $tagParms   = [
    '$name' => 'The variable to count down, named rather than read, written with its <code>$</code>. An application variable, not an engine one (<code>pad*</code>).' ];

  $tagOptions = [];

  $tagSee     = [ 'increment', 'set', 'until', 'while' ];

?>
