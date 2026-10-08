<?php

  $tagAbout   = 'Removes the values of a sequence that belong to a sequence type - the inverse of keep.';
  $tagGroup   = 'sequences';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{remove '1..10', prime} ... {/remove}
{remove prime, rows=5} ... {/remove}
{remove:odd rows=5} ... {/remove:odd}
{remove store, even} ... {/remove}
PAD;

  $tagParms   = [
    'values' => 'Where the values come from: a range <code>\'1..10\'</code>, a stored sequence named bare (not quoted), or left out - the candidates then run from <code>from</code> upward.',
    'type'   => 'The sequence type whose members are removed: <code>prime</code>, <code>odd</code>, <code>square</code>, ... A type with a value is written as an option: <code>multiple=3</code>.' ];

  $tagOptions = [];

  $tagSee     = [ 'keep', 'flag', 'make', 'sequence', 'pull' ];

?>
