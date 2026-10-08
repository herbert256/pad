<?php

  $tagAbout   = 'Lists the pages of the application, read from the file tree, as rows with page, url and lastmod.';
  $tagGroup   = 'navigation';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{sitemap} ... {$page} {$url} {$lastmod} ... {/sitemap}
{sitemap 'dir'} ... {/sitemap}
PAD;

  $tagParms   = [
    'dir' => 'Optional: a directory of the application - <code>\'docs\'</code>; only the pages below it are listed. A name that is no directory, or starts with <code>_</code>, is an error under the strict check.' ];

  $tagOptions = [];

  $tagSee     = [ 'files', 'dir', 'pager', 'meta' ];

?>
