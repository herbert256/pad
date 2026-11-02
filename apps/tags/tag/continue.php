<?php

  $tagAbout   = 'Skips the rest of the current pass of a loop and goes on with its next row.';
  $tagGroup   = 'loops';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{pad:continue/}
{pad:continue 'name'/}
PAD;

  $tagParms   = [
    'level' => 'Optional: which loop. The name of an enclosing tag or its <code>name=</code> (<code>\'items\'</code>), a level number, or a negative offset counting levels up from here. Without it, the nearest enclosing loop - past an <code>{pad:if}</code>, a <code>{pad:case}</code> and any other level that renders once for the row it stands in.' ];

  $tagOptions = [];

  $tagSee     = [ 'break', 'cease', 'while', 'sequence:auto' ];

?>
