<?php

  $tagAbout   = 'Assigns one or more variables, as globals that outlive the level the tag stands in.';
  $tagGroup   = 'values';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{set $name = value}
{set $name = value, $other = value}
PAD;

  $tagParms   = [
    '$name = value' => 'One or more assignments, separated by commas. The value is an expression: a literal, a field, arithmetic (<code>$price * $qty</code>), a comparison.' ];

  $tagOptions = [];

  $tagSee     = [ 'increment', 'decrement', 'data', 'content' ];

?>
