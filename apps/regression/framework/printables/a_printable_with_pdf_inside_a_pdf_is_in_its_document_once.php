<?php

  // A printable with pdf inside a {pdf} pair is a part of that document, once, in its place:
  // the document of the page asked with padPdf is the {pdf}'s - named report, its heading on
  // an A4 page, then the envelope on a page of 220 by 110 mm. The printable asked for a
  // document of its own as well, its level ending inside the pair: its sheets came first, its
  // name won, and the pair's content brought them a second time - envelope.pdf of 3 pages.

  $pdf = padCurl ( $padHost . 'regression/framework/?printables/a_printable_with_pdf_inside_a_pdf_has_no_links_of_its_own&padPdf' );

  preg_match_all ( '#/MediaBox \[0 0 ([0-9.]+) ([0-9.]+)\]#', $pdf ['data'], $boxes, PREG_SET_ORDER );

  $answer = ( preg_match ( '/filename="([^"]*)"/', $pdf ['headers'] ['Content-Disposition'] ?? '', $m ) ? $m [1] : '?' ) . ', '
          . substr_count ( $pdf ['data'], '/Type /Page ' ) . ' pages: '
          . implode ( ', ', array_map ( fn ( $box ) => "$box[1] by $box[2]", $boxes ) );

?>
