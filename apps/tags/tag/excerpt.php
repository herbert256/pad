<?php

  $tagAbout   = 'Cuts a long text to whole words with an ellipsis, centred on the first match of search words, every match marked.';
  $tagGroup   = 'text';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{excerpt $text, words=40}
{excerpt $text, words=24, highlight=$q, ellipsis='...'}
{excerpt $html, words=30, html}
PAD;

  $tagParms   = [
    'text' => 'The text to cut.' ];

  $tagOptions = [
    'words'     => 'The length in words, default 40. A text no longer than that stays whole.',
    'highlight' => 'Search words - a query, split at everything that is no letter or digit. The cut is centred on the first match and every match is put in a <code>&lt;mark&gt;</code>.',
    'html'      => 'Bare option: the text is HTML - its tags (and the content of <code>script</code> and <code>style</code>) are dropped and its entities read before it is cut.',
    'ellipsis'  => 'What stands for the text that was cut off, default <code>…</code>.' ];

  $tagSee     = [ 'markdown', 'diff', 'lorem' ];

?>
