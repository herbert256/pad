<?php

  $tagAbout   = 'Page links for a tag written with the page option: first and last, a window round the current page, previous and next.';
  $tagGroup   = 'navigation';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{pager 'name'}
{pager 'name', window=2, query='p'}
{pager 'name'} ... {$kind} {$page} {$label} {$href} ... @else@ ... {/pager}
PAD;

  $tagParms   = [
    'name' => 'The paged tag - its <code>name=</code>, or the tag\'s own name. Left out, the pager follows the last paged tag of the page.' ];

  $tagOptions = [
    'window' => 'The number of pages shown on each side of the current one; 2 by default. The first and the last page always show, a gap (<code>…</code>) stands for the pages left out.',
    'query'  => 'The request value the links set. By default the variable <code>page=</code> was written with - <code>page=$pg</code> makes the links set <code>pg</code> - else <code>page</code>.' ];

  $tagSee     = [ 'datatable', 'array', 'collection' ];

?>
