<?php

  $tagAbout   = 'Shows values as a collapsible tree in the page, for a local request only, while rendering goes on.';
  $tagGroup   = 'debug';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{debug}
{debug $value, $other}
{debug label=$value}
PAD;

  $tagParms   = [
    'value' => 'Any number of items. A <code>$name</code> shows that field or array as it is - a missing one is shown as <em>missing</em>, not an error; anything else is evaluated as an expression. <code>label=expression</code> gives the box a name of its own. Without items: every field visible where the tag stands.' ];

  $tagOptions = [];

  $tagSee     = [ 'dump', 'trace', 'jsonview' ];

?>
