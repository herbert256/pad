<?php

  $tagAbout   = 'Site search without a search server: the rows of a collection, a data store or a page\'s array a query finds, ranked by BM25, each with a marked excerpt.';
  $tagGroup   = 'database';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{search 'blog', q=$q, fields='title^2, body', lang='en'}{$title} {$score} {!excerpt}@else@...{/search}
{search 'products', q=$q, words=20, excerpt='about'}...{/search}
{search 'blog', q=$q, link='url'/}
PAD;

  $tagParms   = [
    'source' => 'A Markdown collection, <code>_content/&lt;name&gt;/</code> - else a <code>{data}</code> store, a page\'s array (a <code>db()</code> result) or a <code>_data</code> file of the name.' ];

  $tagOptions = [
    'q'       => 'The query: words (any of them), <code>"a phrase"</code> (required, in order), <code>prefix*</code>, <code>-word</code> and <code>-"a phrase"</code> (left out).',
    'fields'  => 'The fields searched with their boosts, <code>\'title^2, body\'</code> - a collection\'s default; other rows every field that holds text in any row.',
    'lang'    => 'The language of the stop words and the stemmer: <code>en</code>, <code>nl</code>, <code>de</code>, <code>fr</code>, <code>es</code> - the locale\'s, else English.',
    'words'   => 'The length of the excerpt in words, default 30.',
    'excerpt' => 'The field the excerpt is cut from, default the last of <code>fields</code>.',
    'link'    => 'The single tag\'s list: a field with the link of a hit - http(s), a page or an anchor.' ];

  $tagSee     = [ 'collection', 'excerpt', 'data' ];

?>
