<?php

  $tagAbout   = 'Leaves a loop at once: the rest of the current pass is dropped and no further row follows.';
  $tagGroup   = 'loops';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{break}
{break 'name'}
PAD;

  $tagParms   = [
    'level' => 'Optional: which loop. The name of an enclosing tag or its <code>name=</code> (<code>\'items\'</code>), a level number, or a negative offset counting levels up from here. Without it, the nearest enclosing loop - past an <code>{if}</code>, a <code>{case}</code> and any other level that renders once for the row it stands in.' ];

  $tagOptions = [];

  $tagSee     = [ 'cease', 'continue', 'while', 'sequence' ];

?>
