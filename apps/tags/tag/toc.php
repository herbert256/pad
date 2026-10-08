<?php

  $tagAbout   = 'A table of contents of the page from its own rendered headings, nested and linked - filled in once the page has rendered.';
  $tagGroup   = 'text';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{toc}
{toc levels='2-4', title='On this page', list='ul'}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'levels' => 'The heading levels listed - a list or a range, <code>\'2,3\'</code> by default, <code>\'2-4\'</code>.',
    'title'  => 'The label above the list, <code>Contents</code> by default; <code>\'\'</code> for none.',
    'list'   => '<code>ol</code> (numbered, the default) or <code>ul</code>.' ];

  $tagSee     = [ 'stack', 'markdown', 'page' ];

?>
