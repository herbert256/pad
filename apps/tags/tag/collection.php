<?php

  $tagAbout   = 'Reads a folder of Markdown files from _content/ and repeats its content once per file, the front matter as fields.';
  $tagGroup   = 'database';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{collection 'name'} ... {/collection}
{collection 'name', slug=$slug} ... @else@ ... {/collection}
{collection 'name', html} ... {/collection}
PAD;

  $tagParms   = [
    'name' => 'The collection: the directory <code>_content/name/</code>, looked up like a <code>_data</code> file - the page\'s directory first, then its parents, then <code>_common</code>.' ];

  $tagOptions = [
    'slug' => 'Only the file of that name (without <code>.md</code>). A slug that names no file, or is not a plain file name, gives no rows - the <code>@else@</code> branch.',
    'html' => 'Bare option: raw HTML in the Markdown bodies is let through. By default it is escaped.' ];

  $tagSee     = [ 'markdown', 'data', 'array', 'files' ];

?>
