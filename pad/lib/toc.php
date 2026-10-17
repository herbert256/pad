<?php

  // padTocFill - the markers a {toc} printed filled in with the headings of the page that
  // holds them, once it has rendered: from exits/exits.php for the page of the request, from
  // start/pad/pad.php for a page a {page} renders, and for the parts of a {pdf}. It runs for
  // every page, so what it does for one without a marker - nothing - is all that is loaded
  // on every request: the filling is padTocFillPage of tags/_lib/toc.php, the {toc} tag's
  // code, included for a page that holds a marker - one the tag printed in this request, or
  // one a cached fragment brought along.

  function padTocFill ( $output ) {

    if ( ! is_string ( $output ) or ! str_contains ( $output, "\u{E0F6}" ) )
      return $output;

    include_once PAD . 'tags/_lib/toc.php';

    return padTocFillPage ( $output );

  }

?>
