<?php

  // A printable inside an item of another - an envelope on a business card - stays in the
  // box of that item in the PDF, its text set there as the screen shows it in the box: one
  // A4 page with both cards. Its sheet started a page of its own inside the outer box, and
  // the boxes after it went to a third page: the card's tail and the whole second card.

  $html = padUnprotect ( padUnescape ( padCode ( <<<'PAD'
{format:auto 'cards'}
[ { "n": "one", "inner": 1 }, { "n": "two", "inner": 0 } ]
{/format:auto}
{printable:cards data='cards'}card {$n}{pad:if $inner}{printable:envelope sender='PAD'}Ann{/printable:envelope}tail{/pad:if}{/printable:cards}
PAD ) ) );

  $pdf = padPdf ( $html, [ 'compress' => FALSE ] );

  preg_match_all ( '#/MediaBox \[0 0 ([0-9.]+) ([0-9.]+)\]#', $pdf, $boxes, PREG_SET_ORDER );
  preg_match_all ( '/stream\n(.*?)\nendstream/s', $pdf, $streams );

  $pages = [];

  foreach ( $boxes as $i => $box ) {
    preg_match_all ( '/\(([^)]*)\) Tj/', $streams [1] [$i] ?? '', $words );
    $pages [] = "$box[1] by $box[2]: " . implode ( ' ', $words [1] );
  }

  $answer = count ( $pages ) . ( count ( $pages ) == 1 ? ' page - ' : ' pages - ' ) . implode ( ' // ', $pages );

?>
