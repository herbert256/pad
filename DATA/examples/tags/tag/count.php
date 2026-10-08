<?php

  $tagAbout   = 'Renders its content only when a named data set or page array holds at least one element.';
  $tagGroup   = 'values';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{count 'name'} ... {/count}
{count 'name'} ... @else@ ... {/count}
PAD;

  $tagParms   = [
    'name' => 'The quoted name of a <code>{data}</code> set or of a page array. A data set of that name goes before a variable.' ];

  $tagOptions = [];

  $tagSee     = [ 'data', 'if', 'bool' ];

?>
