<?php

  $tagAbout   = 'Renders its content when a file or directory is there, its else half when not.';
  $tagGroup   = 'files';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{exists $path} ... {/exists}
{exists $path} ... @else@ ... {/exists}
PAD;

  $tagParms   = [
    'path' => 'The path, used exactly as given - no app, data or <code>_include</code> lookup applies, so build it from <code>APP</code> or <code>DATA</code>.' ];

  $tagOptions = [];

  $tagSee     = [ 'files', 'dir', 'file', 'if' ];

?>
