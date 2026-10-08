<?php

  $tagAbout   = 'Generates a range, a list or one of 80 mathematical sequences, and iterates its values.';
  $tagGroup   = 'sequences';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{sequence '1..10'} ... {/sequence}
{sequence range='1..100', increment=5} ... {/sequence}
{sequence list='3;1;2'} ... {/sequence}
{sequence prime, rows=10, name='p'} ... {/sequence}
{sequence fibonacci, from=1, to=20, stop=100, skip=2} ... {/sequence}
{sequence random, minimal=1, maximal=100, rows=5, unique, randomly} ... {/sequence}
{sequence '1..20', keep, prime, sum} ... {/sequence}
{sequence prime, rows=10, push='primes', toData='primeList'}
{sequence pull='primes', reverse} ... {/sequence}
PAD;

  $tagParms   = [
    'what' => 'A range <code>\'1..10\'</code>, a number <code>10</code> (the same as <code>\'1..10\'</code>), the name of a sequence type - <code>prime</code>, <code>fibonacci</code>, <code>triangular</code>, ... - or the name of a stored sequence.' ];

  $tagOptions = [
    'range'     => 'The range as an option: <code>range=\'20..30\'</code>.',
    'list'      => 'An explicit list of values, separated by <code>;</code>: <code>list=\'3;1;2\'</code>.',
    'from'      => 'Where the generating loop starts (default 1). A value for a type that tests candidates (<code>prime</code>), a position for one that computes the nth term (<code>fibonacci</code>, <code>square</code>).',
    'to'        => 'Where the loop ends, counting the same way as <code>from</code>.',
    'increment' => 'The step of the loop, 1 or more (default 1).',
    'rows'      => 'How many terms to produce - for a sequence this counts produced terms whatever the type, so it is the way to ask for "the first ten".',
    'stop'      => 'End at the first term that reaches this value - that term included.',
    'skip'      => 'Skip the first N candidates the loop offers (not the first N kept).',
    'minimal'   => 'Leave out terms below this value.',
    'maximal'   => 'Leave out terms above this value.',
    'unique'    => 'Bare option: leave out a term that is already in the result.',
    'randomly'  => 'Bare option: take the candidates in random order instead of from low to high.',
    'push'      => 'Store the result under this name, for <a href="?tag/pull">{pull}</a>, <a href="?tag/resume">{resume}</a> and <a href="?tag/action">{action}</a>.',
    'pull'      => 'Start from a stored sequence instead of generating one.',
    'toData'    => 'Also keep the values as a data set of this name, iterated as <code>{name}</code>.',
    'name'      => 'The field the current value is written to, instead of <code>$sequence</code>; the result is also stored under it, like <code>push</code>.',
    'negative'  => 'Bare option: invert the selection of an action - <code>first=3, negative</code> is all but the first three.',
    'keep'      => 'Bare option, with a type: keep only the values that belong to that type - <code>keep, prime</code>. Also <code>remove</code>, <code>flag</code> and <code>make</code>; see those tags.',
    'action'    => 'Any action name as an option, applied to the result in the order written: <code>sum</code>, <code>average</code>, <code>median</code>, <code>count</code>, <code>reverse</code>, <code>sort</code>, <code>shuffle</code>, <code>dedup</code>, <code>first=n</code>, <code>last=n</code>, <code>append=\'store\'</code>, ...' ];

  $tagSee     = [ 'pull', 'keep', 'remove', 'flag', 'make', 'resume', 'action', 'sparkline', 'chart' ];

?>
