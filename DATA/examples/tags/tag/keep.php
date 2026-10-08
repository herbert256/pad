<?php

  $tagAbout   = 'Keeps only the values of a sequence that belong to a sequence type.';
  $tagGroup   = 'sequences';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{keep '1..50', square} ... {/keep}
{keep palindrome, from=100, rows=5} ... {/keep}
{keep:prime from=10, rows=4} ... {/keep:prime}
{keep store, even} ... {/keep}
PAD;

  $tagParms   = [
    'values' => 'Where the values come from: a range <code>\'1..50\'</code>, a stored sequence named bare (<code>primes</code>, not quoted), or left out - the candidates then run from <code>from</code> upward.',
    'type'   => 'The sequence type the values are tested against: <code>prime</code>, <code>even</code>, <code>square</code>, <code>palindrome</code>, ... A type with a value is written as an option: <code>multiple=3</code>.' ];

  $tagOptions = [];

  $tagSee     = [ 'remove', 'flag', 'make', 'sequence', 'pull' ];

?>
