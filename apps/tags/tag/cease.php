<?php

  $tagAbout   = 'Ends a loop gracefully: the current pass finishes, and no further row follows.';
  $tagGroup   = 'loops';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{cease}
{cease 'name'}
PAD;

  $tagParms   = [
    'level' => 'Optional: which loop. The name of an enclosing tag or its <code>name=</code> (<code>\'items\'</code>), a level number, or a negative offset counting levels up from here. Without it, the nearest enclosing level that is not an <code>{if}</code> or <code>{case}</code>.' ];

  $tagOptions = [];

  $tagSee     = [ 'break', 'continue', 'while', 'sequence' ];

?>
