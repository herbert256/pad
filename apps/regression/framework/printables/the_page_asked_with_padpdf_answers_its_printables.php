<?php

  // The page of a printable with pdf, asked with padPdf, answers the printable's sheets as a
  // PDF in place of the page: application/pdf, inline, named by file=, a page of A4 - and
  // with padPdf=download as an attachment.

  $ask    = $padHost . 'regression/framework/?printables/the_pdf_option_links_the_sheets_as_a_document&padPdf';
  $shown  = padCurl ( $ask );
  $saved  = padCurl ( "$ask=download" );

  $answer = substr ( $shown ['data'], 0, 8 ) . ', ' . ( $shown ['headers'] ['Content-Type'] ?? '?' ) . ', '
          . strtok ( $shown ['headers'] ['Content-Disposition'] ?? '?', ';' ) . ' ' . ( preg_match ( '/filename="([^"]*)"/', $shown ['headers'] ['Content-Disposition'] ?? '', $m ) ? $m [1] : '?' )
          . ', ' . substr_count ( $shown ['data'], '/Type /Page ' ) . ' page, '
          . ( preg_match ( '#/MediaBox \[0 0 ([0-9.]+) ([0-9.]+)\]#', $shown ['data'], $m ) ? "$m[1] by $m[2]" : '?' )
          . ' // download: ' . strtok ( $saved ['headers'] ['Content-Disposition'] ?? '?', ';' );

?>
