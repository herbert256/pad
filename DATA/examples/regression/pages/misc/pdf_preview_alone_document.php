<?php

  // The document misc/pdf_preview_alone answers when it is asked for: the whole page, the
  // heading - and not the preview's own links to open and download it, which stood in it as
  // dead blue text.

  $answer = padCurl ( $padHost . 'regression/pages/?misc/pdf_preview_alone&padPdf=1' ) ['data'];
  $text   = '';

  if ( preg_match_all ( '#stream\n(.*?)\nendstream#s', $answer, $streams ) )
    foreach ( $streams [1] as $stream )
      $text .= @gzuncompress ( $stream ) ?: $stream;

  preg_match_all ( '#\(([^)]*)\) Tj#', $text, $found );

  $words = implode ( ' / ', $found [1] );

?>
