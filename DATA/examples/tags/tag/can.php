<?php

  $tagAbout   = 'Renders its content when the user may do something a gate decides, and its else half when not.';
  $tagGroup   = 'access';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{can 'ability'} ... {/can}
{can 'ability', $value} ... {else} ... {/can}
{rows}{can 'ability', $rows} ... {/can}{/rows}
PAD;

  $tagParms   = [
    'ability' => 'The name of a gate defined with <code>padGate ( \'edit-post\', fn ( $user, $post ) =&gt; ... )</code> in a <code>_lib/</code> file.',
    'values'  => 'Optional, any number: what the gate is given after the user - an expression each, an array of the page whole, and the name of an enclosing loop for the row of its occurrence.' ];

  $tagOptions = [];

  $tagSee     = [ 'cannot', 'auth', 'guest', 'feature' ];

?>
