<?php

  $tagAbout   = 'Renders its content only when a named data set or page array holds at least one element.';
  $tagGroup   = 'values';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:count 'name'} ... {/pad:count}
{pad:count 'name'} ... @else@ ... {/pad:count}
PAD;

  $tagParms   = [
    'name' => 'The quoted name of a <code>{format:auto}</code> set or of a page array. A data set of that name goes before a variable.' ];

  $tagOptions = [];

  $tagSee     = [ 'format:auto', 'if', 'bool' ];

?>
