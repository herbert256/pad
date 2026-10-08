<?php

  $tagAbout   = 'Renders its content and adds it to a named stack, for a stack tag elsewhere in the page to print.';
  $tagGroup   = 'layout';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{push 'name'} ... {/push}
{push 'name', once='key'} ... {/push}
{push 'name', once} ... {/push}
PAD;

  $tagParms   = [
    'name' => 'The name of the stack, required.' ];

  $tagOptions = [
    'once' => 'With a key: the first push of that key to this stack is kept, later ones are skipped before their content renders. Bare: a push whose rendered text the stack already holds is dropped.' ];

  $tagSee     = [ 'stack', 'cache', 'page' ];

?>
