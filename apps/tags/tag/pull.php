<?php

  $tagAbout   = 'Iterates a stored sequence, optionally through plays and actions.';
  $tagGroup   = 'sequences';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pull 'store'} ... {/pull}
{pull} ... {/pull}
{pull:store} ... {/pull:store}
{pull 'store', reverse, first=3} ... {/pull}
{pull 'store', keep, prime} ... {/pull}
PAD;

  $tagParms   = [
    'store' => 'The name a sequence was stored under with <code>push=</code> or <code>name=</code>. Left out: the sequence pushed last.' ];

  $tagOptions = [
    'action' => 'Any sequence action, applied to the stored values in the order written: <code>reverse</code>, <code>sort</code>, <code>sum</code>, <code>first=3</code>, <code>last=3</code>, <code>append=\'other\'</code>, ... <code>shift=n</code> and <code>pop=n</code> also take the values out of the store.',
    'keep'   => 'A play with a type: <code>keep, prime</code> keeps the members, and likewise <code>remove</code>, <code>flag</code> and <code>make</code>.' ];

  $tagSee     = [ 'sequence', 'resume', 'action', 'keep' ];

?>
