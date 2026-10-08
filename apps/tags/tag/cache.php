<?php

  $tagAbout   = 'Keeps a named section of the page rendered for a number of seconds - the fragment cache.';
  $tagGroup   = 'output';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{cache 'name'} ... {/cache}
{cache 'name', ttl=300, vary=$value} ... {/cache}
{anytag cache=3600, vary=$value} ... {/anytag}
PAD;

  $tagParms   = [
    'name' => 'The section\'s name - its key within the application, and what <code>padFragmentForget ( \'name\' )</code> drops.' ];

  $tagOptions = [
    'ttl'  => 'The seconds the rendering is kept, 300 when not given.',
    'vary' => 'Whatever else the rendering depends on: <code>vary=$userId</code> keeps a copy per user, <code>vary=$country</code> one per country.' ];

  $tagSee     = [ 'nocache', 'fragment', 'push', 'stack', 'curl' ];

?>
