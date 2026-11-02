<?php

  $tagAbout   = 'Iterates a stored sequence, optionally through plays and actions.';
  $tagGroup   = 'sequences';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:pull 'store'} ... {/pad:pull}
{pad:pull} ... {/pad:pull}
{pull:store} ... {/pull:store}
{pad:pull 'store', reverse, first=3} ... {/pad:pull}
{pad:pull 'store', keep, prime} ... {/pad:pull}
PAD;

  $tagParms   = [
    'store' => 'The name a sequence was stored under with <code>push=</code> or <code>name=</code>. Left out: the sequence pushed last.' ];

  $tagOptions = [
    'action' => 'Any sequence action written as an option (or named with <code>action=\'last|2\'</code>), applied to the stored values in the order written: <code>reverse</code>, <code>sort</code>, <code>sum</code>, <code>first=3</code>, <code>last=3</code>, <code>append=\'other\'</code>, ... <code>shift=n</code> and <code>pop=n</code> also take the values out of the store.',
    'keep'   => 'A play with a type: <code>keep, prime</code> or <code>keep=\'prime\'</code> keeps the members, and likewise <code>remove</code>, <code>flag</code> and <code>make</code>.' ];

  $tagSee     = [ 'sequence:auto', 'resume', 'action:auto', 'keep:auto' ];

?>
