<?php

  // A {pad:toc} inside a {pad:pdf} pair lists the document's headings in the PDF, as on the page:
  // the document was made from the parts before the page's tables of contents were filled,
  // and its contents list was the raw marker. The part is taken as a {pad:pdf} takes it - the
  // request's own store is put back after, so this page stays a page.

  include_once PAD . 'tags/_lib/pdf.php';
  include_once PAD . 'tags/_lib/toc.php';

  $store = &padPdfStore ();
  $was   = $store;

  padPdfAsk ( padTocMarker ( [ 2 ], 'Contents', 'ul' ) . '<h2>One</h2><h2>Two</h2>', [] );

  $html = padPdfHtml ( '' );

  $store = $was;

  $filled = ( ! str_contains ( $html, "\u{E0F6}" ) and preg_match ( '/Contents[\s\S]*One[\s\S]*Two[\s\S]*<h2[^>]*>One/', $html ) )
          ? 'filled' : 'the raw marker';

?>
