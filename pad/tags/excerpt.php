<?php

  // {excerpt $text, words=40, highlight=$q} - a text cut to whole words, lib/excerpt.php: an
  // ellipsis where the text goes on, the cut centred on the first match of the highlight=
  // words - a search query, split into words - and every match in a <mark>. words= is the
  // length (default 40), ellipsis= what stands for the rest (…). html takes the text as
  // HTML: its tags are dropped and its entities read before it is cut. Everything of the
  // text is escaped; only the marks are markup.

  $padExcerptWords = padTagParm ( 'words', 40 );

  if ( ! ctype_digit ( (string) $padExcerptWords ) or (int) $padExcerptWords < 1 ) {
    if ( $padCheckSyntax )
      padError ( "the excerpt has words='" . padMakeSafe ( $padExcerptWords, 20 ) . "' - a number of words, 1 or more" );
    $padExcerptWords = 40;
  }

  return padExcerpt ( padUnprotect ( (string) $padParm ), (int) $padExcerptWords,
                      padUnprotect ( (string) padTagParm ( 'highlight' ) ), (bool) padTagParm ( 'html', FALSE ),
                      padUnprotect ( (string) padTagParm ( 'ellipsis', '…' ) ) );

?>
