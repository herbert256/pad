<?php

  $tagAbout   = 'Skips the rest of the current pass of a loop and goes on with its next row.';
  $tagGroup   = 'loops';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{continue}
{continue 'name'}
PAD;

  $tagParms   = [
    'level' => 'Optional: which loop. The name of an enclosing tag or its <code>name=</code> (<code>\'items\'</code>), a level number, or a negative offset counting levels up from here. Without it, the nearest enclosing level that is not an <code>{if}</code> or <code>{case}</code>.' ];

  $tagOptions = [];

  $tagSee     = [ 'break', 'cease', 'while', 'sequence' ];

?>
