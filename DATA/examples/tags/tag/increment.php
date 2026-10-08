<?php

  $tagAbout   = 'Adds one to a variable, starting it at 1 when it does not exist yet.';
  $tagGroup   = 'values';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{increment $name}
PAD;

  $tagParms   = [
    '$name' => 'The variable to count with, named rather than read, written with its <code>$</code>. An application variable, not an engine one (<code>pad*</code>).' ];

  $tagOptions = [];

  $tagSee     = [ 'decrement', 'set', 'while', 'until' ];

?>
