<?php

  $tagAbout   = 'Transforms the sequence pushed last, in place, through an action or a play.';
  $tagGroup   = 'sequences';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{resume reverse}
{resume add=10}
{resume keep, prime}
{resume first=3}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'action' => 'Any sequence action, as an option of its own or named with <code>action=\'reverse\'</code> - <code>reverse</code>, <code>sort</code>, <code>sum</code>, <code>first=3</code>, ... - or a play with a type: <code>add=10</code> (make), <code>keep, prime</code>, <code>remove, even</code>, <code>flag, odd</code>.' ];

  $tagSee     = [ 'sequence', 'pull', 'action' ];

?>
