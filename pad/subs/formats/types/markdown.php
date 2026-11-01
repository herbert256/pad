<?php

  // markdown - Markdown pipe tables (GitHub Flavored Markdown), read and written: the first
  // table of a text - a README, a page of notes - as rows, and rows as a table whose columns
  // line up, numbers on the right. A cell is its text, its Markdown not rendered; \| is a pipe
  // in a cell and <br> a line break. Known on sight: a first line opening with a | and a row
  // of dashes under it.

  // No Accept header asks for it: an agent's text/markdown asks for the page as Markdown, and
  // a pipe table of its first list is no such thing - padFormat=md asks for the table.

  return [
    'name'      => 'Markdown',
    'mime'      => 'text/markdown',
    'accept'    => [],
    'extension' => [ 'md', 'markdown' ],
    'shape'     => 'rows',
    'lib'       => 'markdown',
    'read'      => fn ( $text, $options ) => padMarkdownRead ( $text ),
    'write'     => fn ( $data, $options ) => padMarkdownWrite ( padFormatRows ( $data ) ),
    'sniff'     => [ 'csv', fn ( $text ) => padFormatMarkdownSniff ( $text ) ],
    'download'  => 'inline'
  ];

?>
