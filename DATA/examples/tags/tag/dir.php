<?php

  $tagAbout   = 'Lists the names in a directory, one occurrence each - the plain version of files.';
  $tagGroup   = 'files';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{dir $path} {$dir} {/dir}
PAD;

  $tagParms   = [
    'path' => 'The directory, used as given - an absolute path, built from <code>APP</code> or <code>DATA</code>. It must lie inside the applications, the engine or DATA.' ];

  $tagOptions = [];

  $tagSee     = [ 'files', 'exists' ];

?>
