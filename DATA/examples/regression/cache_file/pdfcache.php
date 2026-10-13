<?php

  // A {pdf} in a {cache} section answers its document every time it is asked: a section
  // that makes a PDF is not kept, where it was, and on a hit the {pdf} did not run - the
  // first request got the PDF, every later one the page's HTML. pdfcached is asked twice.

  $url = $padHost . 'regression/cache_file/?pdfcached&padPdf';

  $first  = padCurl ( $url );
  $second = padCurl ( $url );

  $types = ( str_starts_with ( (string) ( $first ['data'] ?? '' ), '%PDF' ) ? 'pdf' : 'html' ) . ', then '
         . ( str_starts_with ( (string) ( $second ['data'] ?? '' ), '%PDF' ) ? 'pdf' : 'html' );

?>
