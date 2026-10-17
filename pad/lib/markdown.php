<?php

  // Markdown to HTML - the renderer behind the {markdown} tag and the markdown pipe.
  //
  // There is no Composer in PAD, so this is a small built-in CommonMark subset rather than a
  // library: ATX and setext headings, paragraphs, emphasis and strong emphasis, code spans,
  // fenced and indented code blocks, bullet and ordered lists (nested, tight or loose),
  // links, images, autolinks, block quotes, thematic breaks, hard line breaks and backslash
  // escapes. Reference-style links, tables and footnotes are left out.
  //
  // It is safe for content a visitor wrote: raw HTML in the text is escaped, never passed
  // on, and a link or image whose URL names a scheme other than http, https, mailto, ftp or
  // tel (javascript:, data:, vbscript: ...) loses its URL and keeps only its text. Only the
  // author of a template can let raw HTML through, with the html option of the tag. The
  // escaping leaves an entity that is already there alone, the way the sanitize chain does,
  // so a field the tag form rendered first - already encoded - and the &open; stand-ins of
  // the ignore option arrive in the page as they were meant.
  //
  // $dedent: the text of a {markdown} pair is dedented first - indented along with the
  // template around it, it would otherwise turn into one big code block. A value - the
  // markdown pipe, {markdown $text}, a collection's body - is read as it is written: one
  // that is all indented code lost its code block.
  //
  // padMarkdown is the entry - the tag, the pipe, {collection} and a page's PHP call it; the
  // renderer's parts are tags/_lib/markdown.php, read the first time it runs.

  function padMarkdown ( $text, $html = FALSE, $dedent = FALSE ) {

    include_once PAD . 'tags/_lib/markdown.php';

    if ( ! is_scalar ( $text ) )
      return '';

    $text = str_replace ( [ "\r\n", "\r", "\x1A" ], [ "\n", "\n", '' ], (string) $text );
    $text = padMarkdownTabs ( $text );

    $lines = explode ( "\n", $dedent ? padMarkdownDedent ( $text ) : $text );
    $hold  = [];

    $out = padMarkdownBlocks ( $lines, $html, FALSE, $hold );

    return trim ( padMarkdownRestore ( $out, $hold ) );

  }

?>
