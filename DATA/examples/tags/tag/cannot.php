<?php

  $tagAbout   = 'Renders its content when the user may not do something a gate decides - can turned round.';
  $tagGroup   = 'access';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{cannot 'ability', $value} ... {/cannot}
{cannot 'ability', $value} ... {else} ... {/cannot}
PAD;

  $tagParms   = [
    'ability' => 'The name of a gate defined with <code>padGate</code>.',
    'values'  => 'Optional, any number: what the gate is given after the user, as for <code>{can}</code>.' ];

  $tagOptions = [];

  $tagSee     = [ 'can', 'auth', 'guest' ];

?>
