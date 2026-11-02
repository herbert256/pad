<?php

  $tagAbout   = 'Replaces every value of a sequence with what a sequence type makes of it.';
  $tagGroup   = 'sequences';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{make:range '1..5', add=10} ... {/make:range}
{make:range '1..5', add=10, multiply=2} ... {/make:range}
{make:range '1..6', prime} ... {/make:range}
{make:fibonacci rows=8} ... {/make:fibonacci}
{make:auto store, square} ... {/make:auto}
PAD;

  $tagParms   = [
    'values' => 'Where the values come from: a range <code>\'1..5\'</code>, a stored sequence named bare, or a sequence type to generate.',
    'type'   => 'The sequence type applied to each value. An arithmetic type takes its operand as an option - <code>add=10</code>, <code>multiply=2</code>, <code>modulo=7</code>; a type like <code>prime</code> or <code>square</code> turns n into its nth term.' ];

  $tagOptions = [];

  $tagSee     = [ 'keep:auto', 'remove:auto', 'flag', 'sequence:auto', 'resume' ];

?>
