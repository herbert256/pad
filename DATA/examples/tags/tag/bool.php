<?php

  $tagAbout   = 'Reduces its content or a value to TRUE or FALSE and keeps it under a name, for a later {bool:name} to test.';
  $tagGroup   = 'values';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{pad:bool 'name'} expression {/pad:bool}
{pad:bool 'name', value/}
{bool:name} ... @else@ ... {/bool:name}
PAD;

  $tagParms   = [
    'name'  => 'The name the flag is kept under; <code>{bool:name}</code> ... <code>{/bool:name}</code> or <code>{bool:name}</code> ... <code>{/bool:name}</code> renders its content when it is TRUE. Not the name of a built-in or application tag.',
    'value' => 'Optional, when there is no content: a value whose truth is the flag - blank and <code>\'0\'</code> are FALSE, anything else TRUE. With neither content nor value the flag is FALSE.' ];

  $tagOptions = [];

  $tagSee     = [ 'if', 'content', 'data', 'true', 'false' ];

?>
