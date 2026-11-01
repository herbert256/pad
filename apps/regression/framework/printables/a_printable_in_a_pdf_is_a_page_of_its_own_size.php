<?php

  // In a PDF a printable's sheet is a page of its own size - an envelope DL, 623.62 by 311.81
  // points, a sheet of labels A4 - the first one the document's first page while nothing
  // stands on it, and what the flow sets after the sheets goes on a page of the document's own
  // size, Letter here. The sheets carry no page number, and the one page of the flow none
  // either: a number stands under the pages of a flow of more than one.

  $html = padUnprotect ( padUnescape ( padCode ( <<<'PAD'
{printable:envelope sender='PAD, Utrecht'}Ann Smith{/printable:envelope}
{printable:labels sheet='a4-2x7', copies=3}Fragile{/printable:labels}
<p>After the sheets.</p>
PAD ) ) );

  $pdf = padPdf ( $html, [ 'compress' => FALSE, 'size' => 'Letter' ] );

  preg_match_all ( '#/MediaBox \[0 0 ([0-9.]+) ([0-9.]+)\]#', $pdf, $boxes, PREG_SET_ORDER );
  preg_match_all ( '#stream\n(.*?)\nendstream#s', $pdf, $streams );

  $pages = [];

  foreach ( $boxes as $i => $box ) {
    preg_match_all ( '/\(((?:[^()\\\\]|\\\\.)*)\) Tj/', $streams [1] [$i] ?? '', $texts );
    $pages [] = $box [1] . ' by ' . $box [2] . ': ' . implode ( ', ', $texts [1] );
  }

  $answer = count ( $boxes ) . ' pages - ' . implode ( ' // ', $pages );

?>
