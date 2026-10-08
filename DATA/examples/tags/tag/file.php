<?php

  $tagAbout   = 'Writes its rendered content to a file under DATA/ instead of to the page.';
  $tagGroup   = 'files';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{file dir='exports', name='orders', ext='csv'} ... {/file}
{file dir='reports', name='daily', ext='html', date, stamp, id=8} ... {/file}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'dir'   => 'The directory below DATA/; none writes in DATA/ itself.',
    'name'  => 'The file name, <code>file</code> by default.',
    'ext'   => 'The extension, <code>ext</code> by default.',
    'date'  => 'Bare option: <code>_Y-m-d</code> of today added to the name.',
    'stamp' => 'Bare option: a time stamp added to the name.',
    'id'    => 'A random suffix of this many characters added to the name.' ];

  $tagSee     = [ 'files', 'exists', 'output' ];

?>
