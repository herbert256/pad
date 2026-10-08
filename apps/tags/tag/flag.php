<?php

  $tagAbout   = 'Replaces every value of a sequence with 1 or 0: whether it belongs to a sequence type.';
  $tagGroup   = 'sequences';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{flag '1..10', even} ... {/flag}
{flag prime, from=10, rows=12} ... {/flag}
{flag:prime rows=10} ... {/flag:prime}
{flag store, prime} ... {/flag}
PAD;

  $tagParms   = [
    'values' => 'Where the values come from: a range <code>\'1..10\'</code>, a stored sequence named bare, or left out - the candidates then run from <code>from</code> upward.',
    'type'   => 'The sequence type each value is tested against: <code>prime</code>, <code>even</code>, <code>square</code>, ...' ];

  $tagOptions = [];

  $tagSee     = [ 'keep', 'remove', 'make', 'sequence' ];

?>
