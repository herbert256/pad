<?php

  $tagAbout   = 'Runs one sequence action - reverse, sum, first and the rest - over a store, a range or a type, and iterates the result.';
  $tagGroup   = 'sequences';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{action store, reverse} ... {/action}
{action reverse, store} ... {/action}
{action store, first=2} ... {/action}
{action sum, '31..40'} ... {/action}
{action:reverse store} ... {/action:reverse}
{action:shift store, 2} ... {/action:shift}
PAD;

  $tagParms   = [
    'values' => 'A stored sequence named bare, a range <code>\'31..40\'</code>, or a sequence type with its options (<code>prime, rows=5</code>).',
    'action' => 'The action: <code>reverse</code>, <code>sort</code>, <code>shuffle</code>, <code>sum</code>, <code>average</code>, <code>median</code>, <code>count</code>, <code>dedup</code>, <code>first=n</code>, <code>last=n</code>, <code>shift=n</code>, <code>pop=n</code>, ... The two may come in either order; in the prefix form <code>{action:shift store, 2}</code> the tag names the action and a bare number is its value.' ];

  $tagOptions = [];

  $tagSee     = [ 'pull', 'resume', 'sequence' ];

?>
