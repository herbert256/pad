<?php

  $tagAbout   = 'Scans a directory and iterates its entries, with path, file, ext, item and dir for each.';
  $tagGroup   = 'files';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{files 'dir', base='app'} {$item} {$file} {$ext} {/files}
{files dir='dir', base='data', mask='*.txt', exclude='old*', recursive, onlyFiles, includeHidden} ... {/files}
{files 'dir', base='app', onlyDirs} ... {/files}
PAD;

  $tagParms   = [
    'dir' => 'The directory, relative to what <code>base=</code> names. Also written <code>dir=</code>.' ];

  $tagOptions = [
    'base'          => 'What the directory is relative to: <code>app</code> (the application), <code>data</code> (DATA/) or <code>pad</code> (the path as given); otherwise the file system root. It must lie inside the applications, the engine or DATA.',
    'mask'          => 'Only the names that match this shell pattern - <code>*.pad</code>.',
    'exclude'       => 'Leave out the names that match this shell pattern.',
    'recursive'     => 'Bare option: the subdirectories too - every file below, <code>item</code> holding the path.',
    'onlyFiles'     => 'Bare option: files only.',
    'onlyDirs'      => 'Bare option: directories only.',
    'includeHidden' => 'Bare option: names starting with a dot too.' ];

  $tagSee     = [ 'dir', 'exists', 'file', 'sitemap' ];

?>
