<?php

  // What every request asks of a {pdf} - whether the page answers a document - and padPdf,
  // the writer for a page's own PHP. The writer and the rest of the tag are tags/_lib/pdf.php,
  // included when a page uses the tag, or by padPdf on its call.
  //
  // padPdfStore    the request's {pdf}: whether one asked, the whole page or its parts, the
  //                options of the first
  // padPdfAsked    whether this request answers a document - exits/exits.php asks it for
  //                every page, and includes the writer for one that does
  // padPdf         HTML to the bytes of a PDF - for a page's own PHP too:
  //                padPdf ( $html, [ 'size' => 'Letter', 'title' => 'Report' ] )

  function &padPdfStore () {

    static $store = [ 'asked' => FALSE, 'whole' => FALSE, 'parts' => [], 'opts' => [] ];

    return $store;

  }

  function padPdfAsked () {

    global $padOutputType;

    return padPdfStore () ['asked'] and padLive () === '' and ! in_array ( $padOutputType, [ 'json', 'csv' ], TRUE );

  }

  function padPdf ( $html, $opts = [] ) {

    include_once PAD . 'tags/_lib/pdf.php';

    return padPdfMake ( $html, $opts );

  }

?>
