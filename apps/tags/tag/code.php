<?php

  $tagAbout   = 'Runs its content as PAD source in a separate engine pass and outputs what that pass produced.';
  $tagGroup   = 'pages';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{code} ... {/code}
{code sandbox} ... {/code}
{code reset} ... {/code}
{code clean} ... {/code}
{code function='name'} ... {/code}
{echo $snippet | code}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'sandbox'  => 'Bare option: the pass sees none of the page\'s variables and stores and leaves none behind - what <a href="?tag/sandbox">{sandbox}</a> does.',
    'reset'    => 'Bare option: the level arrays and the data, content, bool and sequence stores are cleared for the pass and restored after it; variables stay visible, and what the pass sets stays behind.',
    'clean'    => 'Bare option: the pass sees everything around it, but every variable it creates or changes is undone afterwards.',
    'function' => 'Runs the pass inside a PHP function of that name, giving it a variable scope of its own; what it creates is copied out afterwards (not with <code>sandbox</code>).' ];

  $tagSee     = [ 'sandbox', 'page', 'ignore', 'pad' ];

?>
