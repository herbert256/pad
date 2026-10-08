<?php

  $tagAbout   = 'Runs its content as PAD source in an isolated pass that cannot see or leave behind anything of the page.';
  $tagGroup   = 'pages';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{sandbox} ... {/sandbox}
{sandbox function='name'} ... {/sandbox}
{echo $snippet | sandbox}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'function' => 'Runs the pass inside a PHP function of that name, with a variable scope of its own; being sandboxed, nothing is copied out.' ];

  $tagSee     = [ 'code', 'page', 'ignore' ];

?>
