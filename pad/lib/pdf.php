<?php

  // PDF documents - the {pdf} tag. What a page renders between {pdf} and {/pdf} is laid out
  // as a PDF and sent instead of the page: an invoice, a ticket, a report made from the same
  // template language as the page, with no library and no browser behind it. The writer is
  // PHP alone: PDF 1.4 with the standard fonts every reader has - Helvetica, Times and
  // Courier, WinAnsi encoded, measured with their AFM widths so lines wrap where a reader
  // draws them - on A4 or Letter pages with margins.
  //
  //   {pdf file='invoice-2026-114', size='A4'}
  //     <h1>Invoice</h1> <table>...</table>
  //   {/pdf}
  //   {pdf download}                      the whole page as the document, as an attachment
  //   {pdf preview} ... {/pdf}            the content on a sheet, with links to its PDF
  //
  // The HTML it reads is a small part of HTML, enough for a document: h1-h6, p, b/strong,
  // i/em, u, code, a (blue, and a link in the document for an http, https or mailto
  // address), br, ul/ol/li (nested), blockquote, pre, hr, table/tr/th/td (a grid with
  // colspan, a header row repeated on every page, align= or style="text-align:...") and img
  // - a JPEG, of www/<application>/ or a data: address, put in the document as it is
  // (DCTDecode). Any other element is read for its text; script, style, svg, forms and the
  // head are left out.
  //
  // padPdfAsk      a {pdf} of the page - its content, or the whole page - and its options
  // padPdfAsked    whether this request answers a document
  // padPdfAnswer   the document for exits/exits.php, its headers set: Content-Type
  //                application/pdf, Content-Disposition inline (or attachment) with a name
  // padPdfSheet    the content as HTML on a sheet of paper - a preview, or a {pdf} that a
  //                {page} renders into another page - with the links to the document
  // padPdf         HTML to the bytes of a PDF - for a page's own PHP too:
  //                padPdf ( $html, [ 'size' => 'Letter', 'title' => 'Report' ] )
  //
  // A page with a {pdf} answers the document only when it is the page a request asked for:
  // rendered by {page} into another page - a preview, a card - the content is shown on its
  // sheet and the response stays the page's. With preview the page asked for shows the
  // sheet too, until it is asked with &padPdf. A data answer (json, csv) and a live
  // region's answer go first. The document carries no date of its making, so the same page
  // is the same bytes.

  const PAD_PDF_SIZES = [ 'a4' => [ 595.28, 841.89 ], 'letter' => [ 612, 792 ], 'a5' => [ 419.53, 595.28 ], 'legal' => [ 612, 1008 ] ];

  const PAD_PDF_FONTS = [
    'helvetica' => [ 'Helvetica', 'Helvetica-Bold', 'Helvetica-Oblique', 'Helvetica-BoldOblique' ],
    'times'     => [ 'Times-Roman', 'Times-Bold', 'Times-Italic', 'Times-BoldItalic' ],
    'courier'   => [ 'Courier', 'Courier-Bold', 'Courier-Oblique', 'Courier-BoldOblique' ] ];

  // The widths of the characters 32 to 255 in WinAnsi, in thousandths of the font size, from
  // Adobe's AFM files of the core fonts. The obliques are as wide as their upright fonts,
  // and every Courier character is 600.

  const PAD_PDF_WIDTHS = [
    'Helvetica' =>
      '278 278 355 556 556 889 667 191 333 333 389 584 278 333 278 278 556 556 556 556 556 556 556 556 556 556 278 278 584 584 584 556 ' .
      '1015 667 667 722 722 667 611 778 722 278 500 667 556 833 722 778 667 778 722 667 611 722 667 944 667 667 611 278 278 278 469 556 ' .
      '333 556 556 500 556 556 278 556 556 222 222 500 222 833 556 556 556 556 333 500 278 556 500 722 500 500 500 334 260 334 584 350 ' .
      '556 350 222 556 333 1000 556 556 333 1000 667 333 1000 350 611 350 350 222 222 333 333 350 556 1000 333 1000 500 333 944 350 500 667 ' .
      '278 333 556 556 556 556 260 556 333 737 370 556 584 333 737 333 400 584 333 333 333 556 537 278 333 333 365 556 834 834 834 611 ' .
      '667 667 667 667 667 667 1000 722 667 667 667 667 278 278 278 278 722 722 778 778 778 778 778 584 778 722 722 722 722 667 667 611 ' .
      '556 556 556 556 556 556 889 500 556 556 556 556 278 278 278 278 556 556 556 556 556 556 556 584 611 556 556 556 556 500 556 500',
    'Helvetica-Bold' =>
      '278 333 474 556 556 889 722 238 333 333 389 584 278 333 278 278 556 556 556 556 556 556 556 556 556 556 333 333 584 584 584 611 ' .
      '975 722 722 722 722 667 611 778 722 278 556 722 611 833 722 778 667 778 722 667 611 722 667 944 667 667 611 333 278 333 584 556 ' .
      '333 556 611 556 611 556 333 611 611 278 278 556 278 889 611 611 611 611 389 556 333 611 556 778 556 556 500 389 280 389 584 350 ' .
      '556 350 278 556 500 1000 556 556 333 1000 667 333 1000 350 611 350 350 278 278 500 500 350 556 1000 333 1000 556 333 944 350 500 667 ' .
      '278 333 556 556 556 556 280 556 333 737 370 556 584 333 737 333 400 584 333 333 333 611 556 278 333 333 365 556 834 834 834 611 ' .
      '722 722 722 722 722 722 1000 722 667 667 667 667 278 278 278 278 722 722 778 778 778 778 778 584 778 722 722 722 722 667 667 611 ' .
      '556 556 556 556 556 556 889 556 556 556 556 556 278 278 278 278 611 611 611 611 611 611 611 584 611 611 611 611 611 556 611 556',
    'Times-Roman' =>
      '250 333 408 500 500 833 778 180 333 333 500 564 250 333 250 278 500 500 500 500 500 500 500 500 500 500 278 278 564 564 564 444 ' .
      '921 722 667 667 722 611 556 722 722 333 389 722 611 889 722 722 556 722 667 556 611 722 722 944 722 722 611 333 278 333 469 500 ' .
      '333 444 500 444 500 444 333 500 500 278 278 500 278 778 500 500 500 500 333 389 278 500 500 722 500 500 444 480 200 480 541 350 ' .
      '500 350 333 500 444 1000 500 500 333 1000 556 333 889 350 611 350 350 333 333 444 444 350 500 1000 333 980 389 333 722 350 444 722 ' .
      '250 333 500 500 500 500 200 500 333 760 276 500 564 333 760 333 400 564 300 300 333 500 453 250 333 300 310 500 750 750 750 444 ' .
      '722 722 722 722 722 722 889 667 611 611 611 611 333 333 333 333 722 722 722 722 722 722 722 564 722 722 722 722 722 722 556 500 ' .
      '444 444 444 444 444 444 667 444 444 444 444 444 278 278 278 278 500 500 500 500 500 500 500 564 500 500 500 500 500 500 500 500',
    'Times-Bold' =>
      '250 333 555 500 500 1000 833 278 333 333 500 570 250 333 250 278 500 500 500 500 500 500 500 500 500 500 333 333 570 570 570 500 ' .
      '930 722 667 722 722 667 611 778 778 389 500 778 667 944 722 778 611 778 722 556 667 722 722 1000 722 722 667 333 278 333 581 500 ' .
      '333 500 556 444 556 444 333 500 556 278 333 556 278 833 556 500 556 556 444 389 333 556 500 722 500 500 444 394 220 394 520 350 ' .
      '500 350 333 500 500 1000 500 500 333 1000 556 333 1000 350 667 350 350 333 333 500 500 350 500 1000 333 1000 389 333 722 350 444 722 ' .
      '250 333 500 500 500 500 220 500 333 747 300 500 570 333 747 333 400 570 300 300 333 556 540 250 333 300 330 500 750 750 750 500 ' .
      '722 722 722 722 722 722 1000 722 667 667 667 667 389 389 389 389 722 722 778 778 778 778 778 570 778 722 722 722 722 722 611 556 ' .
      '500 500 500 500 500 500 722 444 444 444 444 444 278 278 278 278 500 556 500 500 500 500 500 570 500 556 556 556 556 500 556 500',
    'Times-Italic' =>
      '250 333 420 500 500 833 778 214 333 333 500 675 250 333 250 278 500 500 500 500 500 500 500 500 500 500 333 333 675 675 675 500 ' .
      '920 611 611 667 722 611 611 722 722 333 444 667 556 833 667 722 611 722 611 500 556 722 611 833 611 556 556 389 278 389 422 500 ' .
      '333 500 500 444 500 444 278 500 500 278 278 444 278 722 500 500 500 500 389 389 278 500 444 667 444 444 389 400 275 400 541 350 ' .
      '500 350 333 500 556 889 500 500 333 1000 500 333 944 350 556 350 350 333 333 556 556 350 500 889 333 980 389 333 667 350 389 556 ' .
      '250 389 500 500 500 500 275 500 333 760 276 500 675 333 760 333 400 675 300 300 333 500 523 250 333 300 310 500 750 750 750 500 ' .
      '611 611 611 611 611 611 889 667 611 611 611 611 333 333 333 333 722 667 722 722 722 722 722 675 722 722 722 722 722 556 611 500 ' .
      '500 500 500 500 500 500 667 444 444 444 444 444 278 278 278 278 500 500 500 500 500 500 500 675 500 500 500 500 500 444 500 444',
    'Times-BoldItalic' =>
      '250 389 555 500 500 833 778 278 333 333 500 570 250 333 250 278 500 500 500 500 500 500 500 500 500 500 333 333 570 570 570 500 ' .
      '832 667 667 667 722 667 667 722 778 389 500 667 611 889 722 722 611 722 667 556 611 722 667 889 667 611 611 333 278 333 570 500 ' .
      '333 500 500 444 500 444 333 500 556 278 278 500 278 778 556 500 500 500 389 389 278 556 444 667 500 444 389 348 220 348 570 350 ' .
      '500 350 333 500 500 1000 500 500 333 1000 556 333 944 350 611 350 350 333 333 500 500 350 500 1000 333 1000 389 333 722 350 389 611 ' .
      '250 389 500 500 500 500 220 500 333 747 266 500 606 333 747 333 400 570 300 300 333 576 500 250 333 300 300 500 750 750 750 500 ' .
      '667 667 667 667 667 667 944 667 667 667 667 667 389 389 389 389 722 722 722 722 722 722 722 570 722 722 722 722 722 611 611 500 ' .
      '500 500 500 500 500 500 722 444 444 444 444 444 278 278 278 278 500 556 500 500 500 500 500 570 500 556 556 556 556 444 500 444' ];

  // ------------------------------------------------------------------------------------
  // The request: a {pdf} asks, exits/exits.php answers.
  // ------------------------------------------------------------------------------------

  function &padPdfStore () {

    static $store = [ 'asked' => FALSE, 'whole' => FALSE, 'parts' => [], 'opts' => [] ];

    return $store;

  }

  // The first {pdf} of a page sets the options; the content of every one is taken, in
  // order. $html NULL is the single tag: the whole page.

  function padPdfAsk ( $html, $opts ) {

    $store = &padPdfStore ();

    if ( ! $store ['asked'] )
      $store ['opts'] = $opts;

    $store ['asked'] = TRUE;

    if ( $html === NULL )
      $store ['whole'] = TRUE;
    else
      $store ['parts'] [] = $html;

  }

  function padPdfAsked () {

    global $padOutputType;

    return padPdfStore () ['asked'] and padLive () === '' and ! in_array ( $padOutputType, [ 'json', 'csv' ], TRUE );

  }

  // The document in place of the page. The parts are still in the engine's encoding, as a
  // {live} region's are; the whole page is $padOutput itself. Tidy knows only HTML, and the
  // page cache keeps the content type the request started with - both stay out.

  function padPdfAnswer ( $page ) {

    global $padContentType, $padTidy, $padMyTidy, $padCache;

    $store = padPdfStore ();
    $opts  = $store ['opts'];

    $html = $store ['whole'] ? $page : padUnprotect ( padUnescape ( padStackFill ( implode ( "\n", $store ['parts'] ) ) ) );

    $padTidy        = FALSE;
    $padMyTidy      = FALSE;
    $padCache       = FALSE;
    $padContentType = 'application/pdf';

    padHeader ( 'Content-Disposition: ' . ( ( $opts ['download'] ?? FALSE ) ? 'attachment' : 'inline' )
              . '; filename="' . padPdfName ( $opts ['file'] ?? '' ) . '.pdf"' );

    return padPdf ( $html, $opts );

  }

  // The content as HTML on a sheet - the preview of a document, and a {pdf} inside a
  // {page} - with, for a preview, the links that ask this page for its PDF. The rules come
  // once per request; the markup goes into content still to be walked, so it is protected.

  function padPdfSheet ( $content, $file, $nested ) {

    static $styled = FALSE;

    $style = '';

    if ( ! $styled ) {
      $styled = TRUE;
      $style  = padPdfStyle ();
    }

    return padProtect ( $style . '<div class="pad-pdf"><div class="pad-pdf-sheet">' )
         . $content
         . padProtect ( '</div>' . ( $nested ? '' : padPdfLinks ( $file ) ) . '</div>' );

  }

  // "Open invoice.pdf" and "Download": this page again, asked for its document.

  function padPdfLinks ( $file ) {

    global $padGo, $padPage;

    $name = padPdfName ( $file );
    $url  = htmlspecialchars ( $padGo . $padPage . '&padPdf', ENT_QUOTES );

    return '<p class="pad-pdf-links"><a class="pad-pdf-open" href="' . $url . '" type="application/pdf">Open '
         . htmlspecialchars ( $name ) . '.pdf</a> <a class="pad-pdf-download" href="' . $url . '=download"'
         . ' type="application/pdf" download="' . htmlspecialchars ( $name ) . '.pdf">Download</a></p>';

  }

  function padPdfStyle () {

    global $padCsp;

    $nonce = ( is_string ( $padCsp ?? NULL ) and str_contains ( $padCsp, "'nonce'" ) )
           ? ' nonce="' . htmlspecialchars ( padNonce (), ENT_QUOTES ) . '"' : '';

    $roles = [ 'paper' => [ '#ffffff', '#ffffff' ], 'ink'    => [ '#1f1f1f', '#1f1f1f' ],
               'rule'  => [ '#c9c8c3', '#b9b8b2' ], 'head'   => [ '#efefed', '#e2e1dc' ],
               'shade' => [ 'rgba(0,0,0,.12)', 'rgba(0,0,0,.5)' ], 'link' => [ '#1a56b0', '#7fb2f0' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-pdf-$role:$day;";
      $both  .= "--pad-pdf-$role:light-dark($day,$night);";
    }

    return "<style$nonce>:where(.pad-pdf){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-pdf){{$both}}}"
         . '.pad-pdf{max-width:640px;margin:0 auto}'
         . '.pad-pdf-sheet{background:var(--pad-pdf-paper);color:var(--pad-pdf-ink);padding:44px 48px;border-radius:3px;'
         . 'box-shadow:0 1px 3px var(--pad-pdf-shade),0 8px 28px var(--pad-pdf-shade);font:13px/1.45 Helvetica,Arial,sans-serif;text-align:left}'
         . '.pad-pdf-sheet h1{font-size:24px;margin:0 0 12px}.pad-pdf-sheet h2{font-size:17px;margin:18px 0 6px}'
         . '.pad-pdf-sheet h3{font-size:14px;margin:14px 0 4px}.pad-pdf-sheet p{margin:0 0 10px}'
         . '.pad-pdf-sheet a{color:#1a56b0}.pad-pdf-sheet img{max-width:100%;height:auto}'
         . '.pad-pdf-sheet hr{border:0;border-top:1px solid var(--pad-pdf-rule);margin:10px 0}'
         . '.pad-pdf-sheet table{width:100%;border-collapse:collapse;margin:4px 0 12px;font-size:12px}'
         . '.pad-pdf-sheet th,.pad-pdf-sheet td{border:1px solid var(--pad-pdf-rule);padding:5px 6px;text-align:left;vertical-align:top}'
         . '.pad-pdf-sheet th{background:var(--pad-pdf-head)}'
         . '.pad-pdf-sheet [align=right]{text-align:right}.pad-pdf-sheet [align=center]{text-align:center}'
         . '.pad-pdf-links{display:flex;gap:16px;justify-content:center;margin:14px 0 0;font:600 14px/1.4 system-ui,sans-serif}'
         . '.pad-pdf-links a{color:var(--pad-pdf-link)}'
         . '</style>';

  }

  // The file name of the document: file=, else the page's own name.

  function padPdfName ( $file ) {

    global $padPage;

    $name = preg_replace ( '/\.pdf$/i', '', (string) $file );
    $name = trim ( preg_replace ( '/[^A-Za-z0-9._-]+/', '-', basename ( $name !== '' ? $name : (string) $padPage ) ), '.-' );

    return ( $name !== '' ) ? $name : 'document';

  }

  // ------------------------------------------------------------------------------------
  // HTML to PDF.
  // ------------------------------------------------------------------------------------

  // The options: size (A4, Letter, A5, Legal), landscape, font (helvetica, times,
  // courier), title (else the first heading), margin in points, and compress - the
  // content streams deflated, on unless asked otherwise.

  function padPdf ( $html, $opts = [] ) {

    $size = PAD_PDF_SIZES [ strtolower ( (string) ( $opts ['size'] ?? 'a4' ) ) ] ?? PAD_PDF_SIZES ['a4'];

    if ( $opts ['landscape'] ?? FALSE )
      $size = [ $size [1], $size [0] ];

    $family = strtolower ( (string) ( $opts ['font'] ?? 'helvetica' ) );
    $margin = (float) ( $opts ['margin'] ?? 56 );

    $doc = [ 'w'        => $size [0],
             'h'        => $size [1],
             'margin'   => $margin,
             'family'   => isset ( PAD_PDF_FONTS [$family] ) ? $family : 'helvetica',
             'pages'    => [],
             'y'        => 0,
             'gap'      => 0,
             'top'      => TRUE,
             'fonts'    => [],
             'images'   => [],
             'marker'   => NULL,
             'title'    => (string) ( $opts ['title'] ?? '' ),
             'heading'  => '' ];

    padPdfPage ( $doc );

    $dom = new DOMDocument ();
    $old = libxml_use_internal_errors ( TRUE );

    $dom->loadHTML ( '<?xml encoding="utf-8"?><html><body>' . $html . '</body></html>', LIBXML_NONET | LIBXML_COMPACT );

    libxml_clear_errors ();
    libxml_use_internal_errors ( $old );

    $body = $dom->getElementsByTagName ( 'body' )->item ( 0 );

    if ( $body )
      padPdfFlow ( $doc, $body, padPdfContext () );

    padPdfNumbers ( $doc );

    return padPdfWrite ( $doc, ( $opts ['compress'] ?? TRUE ) and function_exists ( 'gzcompress' ) );

  }

  function padPdfContext () {

    return [ 'indent' => 0, 'right' => 0, 'size' => 10.5, 'bold' => FALSE, 'italic' => FALSE, 'mono' => FALSE,
             'under' => FALSE, 'link' => '', 'align' => 'left', 'color' => '0.12 0.12 0.12', 'pre' => FALSE ];

  }

  // ------------------------------------------------------------------------------------
  // The flow: blocks one under the other, the inline content of each as wrapped lines.
  // ------------------------------------------------------------------------------------

  // The children of a block: text and inline elements gather into a paragraph that is set
  // when a block comes, or at the end.

  function padPdfFlow ( &$doc, $node, $ctx ) {

    $runs = [];

    foreach ( $node->childNodes as $child ) {

      $name = ( $child instanceof DOMElement ) ? strtolower ( $child->tagName ) : '';

      if ( $name !== '' and padPdfSkip ( $name ) )
        continue;

      if ( $name !== '' and padPdfBlock ( $name ) ) {
        padPdfParagraph ( $doc, $runs, $ctx );
        $runs = [];
        padPdfElement ( $doc, $child, $ctx );
        continue;
      }

      if ( $name == 'img' ) {
        padPdfParagraph ( $doc, $runs, $ctx );
        $runs = [];
        padPdfImage ( $doc, $child, $ctx );
        continue;
      }

      padPdfInline ( $child, $ctx, $runs );

    }

    padPdfParagraph ( $doc, $runs, $ctx );

  }

  function padPdfSkip ( $name ) {

    return in_array ( $name, [ 'head', 'script', 'style', 'title', 'meta', 'link', 'noscript', 'template', 'svg',
                               'iframe', 'object', 'video', 'audio', 'canvas', 'input', 'select', 'button', 'textarea', 'map' ], TRUE );

  }

  function padPdfBlock ( $name ) {

    return in_array ( $name, [ 'p', 'div', 'section', 'article', 'header', 'footer', 'main', 'aside', 'nav', 'form',
                               'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'hr', 'table', 'blockquote', 'pre',
                               'figure', 'figcaption', 'address', 'dl', 'dt', 'dd', 'fieldset', 'details', 'summary',
                               'caption', 'center' ], TRUE );

  }

  // A block element: its spacing, its context for the content, and the content.

  function padPdfElement ( &$doc, $el, $ctx ) {

    $name = strtolower ( $el->tagName );
    $ctx  = padPdfAlign ( $el, $ctx );

    if ( preg_match ( '/^h([1-6])$/', $name, $m ) ) {

      $level = (int) $m [1];
      $ctx ['size'] = [ 1 => 20, 2 => 15, 3 => 12.5, 4 => 11, 5 => 10.5, 6 => 10.5 ] [$level];
      $ctx ['bold'] = TRUE;
      $ctx ['color'] = '0.05 0.05 0.05';

      if ( $doc ['heading'] === '' )
        $doc ['heading'] = trim ( preg_replace ( '/\s+/', ' ', $el->textContent ) );

      padPdfSpace ( $doc, [ 1 => 6, 2 => 16, 3 => 12, 4 => 10, 5 => 8, 6 => 8 ] [$level] );
      padPdfFlow ( $doc, $el, $ctx );
      padPdfAfter ( $doc, [ 1 => 10, 2 => 6, 3 => 4, 4 => 3, 5 => 3, 6 => 3 ] [$level] );
      return;

    }

    switch ( $name ) {

      case 'p': case 'address': case 'figcaption': case 'summary': case 'caption':

        if ( $name == 'address' ) $ctx ['italic'] = TRUE;
        if ( $name == 'summary' ) $ctx ['bold']   = TRUE;

        if ( $name == 'figcaption' or $name == 'caption' ) {
          $ctx ['size']  = 9;
          $ctx ['color'] = '0.35 0.35 0.35';
        }

        padPdfSpace ( $doc, 0 );
        padPdfFlow ( $doc, $el, $ctx );
        padPdfAfter ( $doc, 8 );
        return;

      case 'ul': case 'ol':

        padPdfSpace ( $doc, 0 );

        $number = max ( 1, (int) ( $el->getAttribute ( 'start' ) ?: 1 ) );
        $inner  = $ctx;
        $inner ['indent'] += 18;

        foreach ( $el->childNodes as $item ) {

          if ( ! ( $item instanceof DOMElement ) or strtolower ( $item->tagName ) != 'li' )
            continue;

          $doc ['marker'] = [ $name == 'ol' ? ( $number++ ) . '.' : "\x95", $inner ];

          padPdfFlow ( $doc, $item, padPdfAlign ( $item, $inner ) );
          padPdfMarker ( $doc );
          padPdfAfter ( $doc, 3 );

        }

        padPdfAfter ( $doc, 8 );
        return;

      case 'li':

        $inner = $ctx;
        $inner ['indent'] += 18;
        $doc ['marker'] = [ "\x95", $inner ];
        padPdfFlow ( $doc, $el, $inner );
        padPdfMarker ( $doc );
        padPdfAfter ( $doc, 3 );
        return;

      case 'blockquote': case 'dd':

        $ctx ['indent'] += 20;

        if ( $name == 'blockquote' ) {
          $ctx ['italic'] = TRUE;
          $ctx ['color']  = '0.3 0.3 0.3';
        }

        padPdfSpace ( $doc, 0 );
        padPdfFlow ( $doc, $el, $ctx );
        padPdfAfter ( $doc, 8 );
        return;

      case 'dt':

        $ctx ['bold'] = TRUE;
        padPdfSpace ( $doc, 0 );
        padPdfFlow ( $doc, $el, $ctx );
        padPdfAfter ( $doc, 2 );
        return;

      case 'pre':

        $ctx ['mono'] = TRUE;
        $ctx ['pre']  = TRUE;
        $ctx ['size'] = 9;
        padPdfSpace ( $doc, 0 );
        padPdfFlow ( $doc, $el, $ctx );
        padPdfAfter ( $doc, 8 );
        return;

      case 'hr':

        padPdfSpace ( $doc, 6 );
        padPdfRoom ( $doc, 1 );
        $x0 = $doc ['margin'] + $ctx ['indent'];
        $x1 = $doc ['w'] - $doc ['margin'] - $ctx ['right'];
        padPdfOps ( $doc, '0.78 0.78 0.78 RG 0.75 w ' . padPdfN ( $x0 ) . ' ' . padPdfN ( $doc ['y'] ) . ' m '
                        . padPdfN ( $x1 ) . ' ' . padPdfN ( $doc ['y'] ) . ' l S' );
        $doc ['y'] -= 1;
        padPdfAfter ( $doc, 8 );
        return;

      case 'table':

        padPdfSpace ( $doc, 2 );
        padPdfTable ( $doc, $el, $ctx );
        padPdfAfter ( $doc, 10 );
        return;

      default:

        padPdfFlow ( $doc, $el, $ctx );

    }

  }

  // align="right" or style="text-align:right" on an element sets its lines.

  function padPdfAlign ( $el, $ctx ) {

    $align = strtolower ( trim ( $el->getAttribute ( 'align' ) ) );

    if ( preg_match ( '/text-align\s*:\s*(left|right|center|justify)/i', $el->getAttribute ( 'style' ), $m ) )
      $align = strtolower ( $m [1] );

    if ( strtolower ( $el->tagName ) == 'center' )
      $align = 'center';

    if ( in_array ( $align, [ 'left', 'right', 'center' ], TRUE ) )
      $ctx ['align'] = $align;
    elseif ( $align == 'justify' )
      $ctx ['align'] = 'left';

    return $ctx;

  }

  // The inline content as runs: [ text, context ] - the context holds the font, the colour
  // and the link - and [ NULL ] for a line break. A block met inside inline content breaks
  // the line around its text.

  function padPdfInline ( $node, $ctx, &$runs ) {

    if ( $node instanceof DOMText ) {
      $runs [] = [ $node->data, $ctx ];
      return;
    }

    if ( ! ( $node instanceof DOMElement ) )
      return;

    $name = strtolower ( $node->tagName );

    if ( padPdfSkip ( $name ) )
      return;

    if ( $name == 'br' ) {
      $runs [] = [ NULL, $ctx ];
      return;
    }

    if ( $name == 'img' ) {
      $alt = trim ( $node->getAttribute ( 'alt' ) );
      if ( $alt !== '' )
        $runs [] = [ $alt, $ctx ];
      return;
    }

    switch ( $name ) {
      case 'b': case 'strong': case 'th':               $ctx ['bold']   = TRUE; break;
      case 'i': case 'em': case 'cite': case 'var':
      case 'dfn':                                       $ctx ['italic'] = TRUE; break;
      case 'u': case 'ins':                             $ctx ['under']  = TRUE; break;
      case 'code': case 'kbd': case 'samp': case 'tt':  $ctx ['mono']   = TRUE; break;
      case 'a':
        $href = trim ( $node->getAttribute ( 'href' ) );
        if ( $href !== '' ) {
          $ctx ['color'] = '0.1 0.34 0.69';
          $ctx ['under'] = TRUE;
          $ctx ['link']  = preg_match ( '#^(https?://|mailto:)#i', $href ) ? $href : '';
        }
        break;
    }

    $block = padPdfBlock ( $name );

    if ( $block and $runs )
      $runs [] = [ NULL, $ctx ];

    foreach ( $node->childNodes as $child )
      padPdfInline ( $child, $ctx, $runs );

    if ( $block )
      $runs [] = [ NULL, $ctx ];

  }

  // ------------------------------------------------------------------------------------
  // Paragraphs: runs to words, words to lines, lines to the page.
  // ------------------------------------------------------------------------------------

  function padPdfParagraph ( &$doc, $runs, $ctx ) {

    $lines = padPdfLines ( $doc, $runs, $ctx, padPdfWidthLeft ( $doc, $ctx ) );

    if ( ! $lines )
      return;

    $height = $ctx ['size'] * 1.38;

    foreach ( $lines as $line ) {

      padPdfRoom ( $doc, $height );

      $doc ['y'] -= $height;

      padPdfMarker ( $doc, $doc ['y'] + $ctx ['size'] * 0.3 );

      padPdfLine ( $doc, $line, $doc ['margin'] + $ctx ['indent'], padPdfWidthLeft ( $doc, $ctx ), $doc ['y'] + $ctx ['size'] * 0.3, $ctx ['align'] );

    }

  }

  function padPdfWidthLeft ( $doc, $ctx ) {

    return $doc ['w'] - 2 * $doc ['margin'] - $ctx ['indent'] - $ctx ['right'];

  }

  // Words are pieces of text in one or more fonts with no space between them - <b>12</b>%
  // is one word - and a line is the words that fit, with the spaces between them. A word
  // longer than a line is cut where it reaches the edge. In a <pre> every line of the text
  // is a line, its spaces kept.

  function padPdfLines ( $doc, $runs, $ctx, $width ) {

    $words  = [];
    $word   = [];
    $space  = FALSE;
    $before = FALSE;

    foreach ( $runs as list ( $text, $style ) ) {

      if ( $text === NULL ) {
        if ( $word ) $words [] = [ $word, $before ];
        $words [] = NULL;
        $word  = [];
        $space = FALSE;
        continue;
      }

      if ( $style ['pre'] ) {
        foreach ( preg_split ( '/(\R)/', str_replace ( "\t", '    ', $text ), -1, PREG_SPLIT_DELIM_CAPTURE ) as $part ) {
          if ( preg_match ( '/^\R$/D', $part ) ) {
            $words [] = [ $word, $before ];
            $words [] = NULL;
            $word   = [];
            $before = FALSE;
          } elseif ( $part !== '' )
            $word [] = [ padPdfText ( $part ), $style ];
        }
        continue;
      }

      foreach ( preg_split ( '/([ \t\r\n\f]+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE ) as $part ) {

        if ( $part === '' )
          continue;

        if ( trim ( $part, " \t\r\n\f" ) === '' ) {
          if ( $word )
            $words [] = [ $word, $before ];
          $word  = [];
          $space = TRUE;
          continue;
        }

        if ( ! $word ) {
          $before = $space;
          $space  = FALSE;
        }

        $word [] = [ padPdfText ( $part ), $style ];

      }

    }

    if ( $word )
      $words [] = [ $word, $before ];

    while ( $words and end ( $words ) === NULL )
      array_pop ( $words );

    // The lines, greedy: a word goes on the line when it fits, its space before it.

    $lines = [];
    $line  = [];
    $used  = 0;

    foreach ( $words as $one ) {

      if ( $one === NULL ) {
        $lines [] = $line;
        $line = [];
        $used = 0;
        continue;
      }

      list ( $pieces, $spaced ) = $one;

      $wide = 0;
      foreach ( $pieces as list ( $text, $style ) )
        $wide += padPdfWidth ( $doc, $text, $style );

      $gap = ( $line and $spaced ) ? padPdfWidth ( $doc, ' ', $pieces [0] [1] ) : 0;

      if ( $line and $used + $gap + $wide > $width + 0.01 ) {
        $lines [] = $line;
        $line = [];
        $used = 0;
        $gap  = 0;
      }

      if ( ! $line and $wide > $width ) {
        foreach ( padPdfCut ( $doc, $pieces, $width ) as $k => $part ) {
          if ( $k ) {
            $lines [] = $line;
            $line = [];
          }
          $line = $part;
        }
        $used = 0;
        foreach ( $line as list ( $text, $style ) )
          $used += padPdfWidth ( $doc, $text, $style );
        continue;
      }

      if ( $gap )
        $line [] = [ ' ', [ 'under' => FALSE, 'link' => '' ] + $pieces [0] [1] ];

      foreach ( $pieces as $piece )
        $line [] = $piece;

      $used += $gap + $wide;

    }

    if ( $line )
      $lines [] = $line;

    return $lines;

  }

  // A word wider than the line, cut into lines' worth of characters.

  function padPdfCut ( $doc, $pieces, $width ) {

    $parts = [ [] ];
    $used  = 0;

    foreach ( $pieces as list ( $text, $style ) ) {

      $chunk = '';

      for ( $i = 0; $i < strlen ( $text ); $i++ ) {
        $w = padPdfWidth ( $doc, $text [$i], $style );
        if ( $used + $w > $width and ( $chunk !== '' or $parts [ count ( $parts ) - 1 ] ) ) {
          if ( $chunk !== '' )
            $parts [ count ( $parts ) - 1 ] [] = [ $chunk, $style ];
          $parts [] = [];
          $chunk = '';
          $used  = 0;
        }
        $chunk .= $text [$i];
        $used  += $w;
      }

      if ( $chunk !== '' )
        $parts [ count ( $parts ) - 1 ] [] = [ $chunk, $style ];

    }

    return $parts;

  }

  // One line on the page at its baseline: the pieces of one font and colour together as one
  // string, a link underlined and, for an address of its own, a link of the document.

  function padPdfLine ( &$doc, $line, $x, $width, $base, $align ) {

    $wide = 0;

    foreach ( $line as list ( $text, $style ) )
      $wide += padPdfWidth ( $doc, $text, $style );

    while ( $line and end ( $line ) [0] === ' ' ) {
      $last = array_pop ( $line );
      $wide -= padPdfWidth ( $doc, ' ', $last [1] );
    }

    if ( $align == 'right' )
      $x += $width - $wide;
    elseif ( $align == 'center' )
      $x += ( $width - $wide ) / 2;

    $groups = [];

    foreach ( $line as list ( $text, $style ) ) {
      $key = padPdfFont ( $doc, $style ) . '|' . $style ['size'] . '|' . $style ['color'] . '|' . (int) $style ['under'] . '|' . $style ['link'];
      if ( $groups and end ( $groups ) [0] === $key )
        $groups [ count ( $groups ) - 1 ] [1] .= $text;
      else
        $groups [] = [ $key, $text, $style ];
    }

    foreach ( $groups as list ( $key, $text, $style ) ) {

      $font = padPdfFont ( $doc, $style );
      $wide = padPdfWidth ( $doc, $text, $style );

      padPdfOps ( $doc, "BT {$style ['color']} rg /$font " . padPdfN ( $style ['size'] ) . ' Tf '
                      . padPdfN ( $x ) . ' ' . padPdfN ( $base ) . ' Td (' . padPdfString ( $text ) . ') Tj ET' );

      if ( $style ['under'] and trim ( $text ) !== '' ) {
        $y = $base - $style ['size'] * 0.12;
        padPdfOps ( $doc, "{$style ['color']} RG " . padPdfN ( $style ['size'] * 0.06 ) . ' w '
                        . padPdfN ( $x ) . ' ' . padPdfN ( $y ) . ' m ' . padPdfN ( $x + $wide ) . ' ' . padPdfN ( $y ) . ' l S' );
      }

      if ( $style ['link'] !== '' and trim ( $text ) !== '' )
        $doc ['pages'] [ count ( $doc ['pages'] ) - 1 ] ['links'] []
          = [ $x, $base - $style ['size'] * 0.25, $x + $wide, $base + $style ['size'] * 0.85, $style ['link'] ];

      $x += $wide;

    }

  }

  // The marker of a list item - a bullet or a number - right before the first line of the
  // item, when that line is set; an item with no line of its own gets it at the cursor.

  function padPdfMarker ( &$doc, $base = NULL ) {

    if ( $doc ['marker'] === NULL )
      return;

    list ( $text, $ctx ) = $doc ['marker'];

    $doc ['marker'] = NULL;

    if ( $base === NULL )
      return;

    $style = $ctx;
    $style ['bold'] = $style ['italic'] = $style ['under'] = $style ['mono'] = FALSE;
    $style ['link'] = '';

    $wide = padPdfWidth ( $doc, $text, $style );
    $x    = $doc ['margin'] + $ctx ['indent'] - 6 - $wide;

    padPdfOps ( $doc, "BT {$style ['color']} rg /" . padPdfFont ( $doc, $style ) . ' ' . padPdfN ( $style ['size'] ) . ' Tf '
                    . padPdfN ( $x ) . ' ' . padPdfN ( $base ) . ' Td (' . padPdfString ( $text ) . ') Tj ET' );

  }

  // ------------------------------------------------------------------------------------
  // Tables: a grid, its columns as wide as their content asks, the whole width filled.
  // ------------------------------------------------------------------------------------

  function padPdfTable ( &$doc, $table, $ctx ) {

    $rows = [];

    padPdfTableRows ( $table, $rows );

    if ( ! $rows )
      return;

    $pad   = 5;
    $cells = [];
    $count = 0;

    foreach ( $rows as $r => $tr ) {

      $col = 0;

      foreach ( $tr->childNodes as $td ) {

        if ( ! ( $td instanceof DOMElement ) or ! in_array ( strtolower ( $td->tagName ), [ 'td', 'th' ], TRUE ) )
          continue;

        $head  = strtolower ( $td->tagName ) == 'th';
        $span  = max ( 1, min ( 50, (int) ( $td->getAttribute ( 'colspan' ) ?: 1 ) ) );
        $style = padPdfAlign ( $td, padPdfAlign ( $tr, $ctx ) );

        $style ['size']   = 9.5;
        $style ['indent'] = 0;
        $style ['right']  = 0;

        if ( $head )
          $style ['bold'] = TRUE;

        $runs = [];
        foreach ( $td->childNodes as $child )
          padPdfInline ( $child, $style, $runs );

        $cells [$r] [] = [ 'col' => $col, 'span' => $span, 'head' => $head, 'runs' => $runs, 'style' => $style ];

        $col += $span;

      }

      $count = max ( $count, $col );

    }

    if ( ! $count )
      return;

    // The natural width of a column is its widest cell on one line, its least the longest
    // word; the widths fill the room, by the natural widths when they fit, else the least
    // ones and what is left shared by what each would like more.

    $room    = padPdfWidthLeft ( $doc, $ctx );
    $natural = array_fill ( 0, $count, 2 * $pad + 12 );
    $least   = array_fill ( 0, $count, 2 * $pad + 12 );

    foreach ( $cells as $row )
      foreach ( $row as $cell ) {
        if ( $cell ['span'] != 1 )
          continue;
        list ( $nat, $min ) = padPdfMeasure ( $doc, $cell ['runs'], $cell ['style'] );
        $natural [ $cell ['col'] ] = max ( $natural [ $cell ['col'] ], $nat + 2 * $pad );
        $least   [ $cell ['col'] ] = max ( $least   [ $cell ['col'] ], $min + 2 * $pad );
      }

    $sumNat = array_sum ( $natural );
    $sumMin = array_sum ( $least );
    $widths = [];

    foreach ( $natural as $c => $nat )
      if ( $sumNat <= $room )
        $widths [$c] = $nat + ( $room - $sumNat ) * $nat / $sumNat;
      elseif ( $sumMin <= $room )
        $widths [$c] = $least [$c] + ( $room - $sumMin ) * ( $nat - $least [$c] ) / max ( 0.01, $sumNat - $sumMin );
      else
        $widths [$c] = $room * $least [$c] / $sumMin;

    // The rows laid out, then set: a row that does not fit goes to the next page, the header
    // row before it.

    $laid = [];

    foreach ( $cells as $r => $row ) {

      $height = 0;
      $set    = [];

      foreach ( $row as $cell ) {
        $wide   = array_sum ( array_slice ( $widths, $cell ['col'], $cell ['span'] ) );
        $lines  = padPdfLines ( $doc, $cell ['runs'], $cell ['style'], $wide - 2 * $pad );
        $set [] = [ $cell, $wide, $lines ];
        $height = max ( $height, count ( $lines ) * $cell ['style'] ['size'] * 1.3 + 2 * $pad );
      }

      $laid [] = [ $set, max ( $height, 9.5 * 1.3 + 2 * $pad ), ! array_filter ( $row, fn ( $c ) => ! $c ['head'] ) ];

    }

    $header = ( $laid and $laid [0] [2] ) ? $laid [0] : NULL;

    foreach ( $laid as $k => $row ) {

      if ( padPdfRoom ( $doc, $row [1] ) and $header and $k > 0 )
        padPdfRow ( $doc, $header, $widths, $ctx, $pad );

      padPdfRow ( $doc, $row, $widths, $ctx, $pad );

    }

  }

  function padPdfTableRows ( $node, &$rows ) {

    foreach ( $node->childNodes as $child ) {

      if ( ! ( $child instanceof DOMElement ) )
        continue;

      $name = strtolower ( $child->tagName );

      if ( $name == 'tr' )
        $rows [] = $child;
      elseif ( in_array ( $name, [ 'thead', 'tbody', 'tfoot' ], TRUE ) )
        padPdfTableRows ( $child, $rows );

    }

  }

  // The width of the runs on one line, and of their widest word.

  function padPdfMeasure ( $doc, $runs, $style ) {

    $natural = $least = $line = 0;

    foreach ( padPdfLines ( $doc, $runs, $style, 1e6 ) as $one ) {
      $line = 0;
      foreach ( $one as list ( $text, $st ) ) {
        $line += padPdfWidth ( $doc, $text, $st );
        if ( $text !== ' ' )
          $least = max ( $least, padPdfWidth ( $doc, $text, $st ) );
      }
      $natural = max ( $natural, $line );
    }

    return [ $natural, $least ];

  }

  function padPdfRow ( &$doc, $row, $widths, $ctx, $pad ) {

    list ( $set, $height ) = $row;

    $top = $doc ['y'];
    $x   = $doc ['margin'] + $ctx ['indent'];

    foreach ( $set as list ( $cell, $wide, $lines ) ) {

      $box = padPdfN ( $x ) . ' ' . padPdfN ( $top - $height ) . ' ' . padPdfN ( $wide ) . ' ' . padPdfN ( $height ) . ' re';

      if ( $cell ['head'] )
        padPdfOps ( $doc, "0.94 0.94 0.93 rg $box f" );

      padPdfOps ( $doc, "0.74 0.74 0.72 RG 0.5 w $box S" );

      $size = $cell ['style'] ['size'];
      $base = $top - $pad - $size;

      foreach ( $lines as $line ) {
        padPdfLine ( $doc, $line, $x + $pad, $wide - 2 * $pad, $base + $size * 0.15, $cell ['style'] ['align'] );
        $base -= $size * 1.3;
      }

      $x += $wide;

    }

    $doc ['y'] = $top - $height;

  }

  // ------------------------------------------------------------------------------------
  // Pictures: a JPEG goes into the document as it is.
  // ------------------------------------------------------------------------------------

  function padPdfImage ( &$doc, $el, $ctx ) {

    $file = padPdfImageData ( trim ( $el->getAttribute ( 'src' ) ) );
    $alt  = trim ( $el->getAttribute ( 'alt' ) );

    if ( $file === NULL ) {
      if ( $alt !== '' ) {
        $ctx ['italic'] = TRUE;
        $ctx ['color']  = '0.4 0.4 0.4';
        padPdfParagraph ( $doc, [ [ "[$alt]", $ctx ] ], $ctx );
      }
      return;
    }

    list ( $data, $pw, $ph, $channels ) = $file;

    $key = md5 ( $data );

    if ( ! isset ( $doc ['images'] [$key] ) )
      $doc ['images'] [$key] = [ 'name' => 'Im' . ( count ( $doc ['images'] ) + 1 ), 'data' => $data,
                                 'w' => $pw, 'h' => $ph, 'channels' => $channels ];

    // The size is the element's width and height in CSS pixels - 0.75 of a point - else the
    // picture's own, never wider than the column nor taller than a page.

    $w = (float) $el->getAttribute ( 'width' );
    $h = (float) $el->getAttribute ( 'height' );

    if ( $w and ! $h ) $h = $w * $ph / $pw;
    if ( $h and ! $w ) $w = $h * $pw / $ph;
    if ( ! $w ) list ( $w, $h ) = [ $pw, $ph ];

    $w *= 0.75;
    $h *= 0.75;

    $room = padPdfWidthLeft ( $doc, $ctx );
    $tall = $doc ['h'] - 2 * $doc ['margin'] - 20;

    $scale = min ( 1, $room / $w, $tall / $h );
    $w    *= $scale;
    $h    *= $scale;

    padPdfSpace ( $doc, 2 );
    padPdfRoom ( $doc, $h );

    $x = $doc ['margin'] + $ctx ['indent'];

    if ( $ctx ['align'] == 'center' ) $x += ( $room - $w ) / 2;
    if ( $ctx ['align'] == 'right'  ) $x += $room - $w;

    $doc ['y'] -= $h;

    padPdfOps ( $doc, 'q ' . padPdfN ( $w ) . ' 0 0 ' . padPdfN ( $h ) . ' ' . padPdfN ( $x ) . ' ' . padPdfN ( $doc ['y'] )
                    . ' cm /' . $doc ['images'] [$key] ['name'] . ' Do Q' );

    padPdfAfter ( $doc, 6 );

  }

  // The bytes of a JPEG an address names: a data: address, or a file of www/<application>/
  // by its name or by the root-relative address {asset} and {img} write. Anything else - a
  // PNG, another site's picture - is NULL, and the alt text stands in.

  function padPdfImageData ( $src ) {

    global $padApp, $padRoot;

    if ( preg_match ( '#^data:image/jpe?g;base64,(.*)$#is', $src, $m ) ) {
      $data = base64_decode ( preg_replace ( '/\s+/', '', $m [1] ), TRUE );
    } else {

      $src  = rawurldecode ( preg_replace ( '/[?#].*$/s', '', $src ) );
      $base = ( $padRoot ?? '/' ) . "$padApp/";

      if ( str_starts_with ( $src, $base ) )
        $src = substr ( $src, strlen ( $base ) );

      if ( ! preg_match ( '#^[A-Za-z0-9_@+~-][A-Za-z0-9_.@+~/-]*$#D', $src )
           or in_array ( '..', explode ( '/', $src ), TRUE ) )
        return NULL;

      $dir  = realpath ( dirname ( APPS ) . "/www/$padApp" );
      $path = ( $dir === FALSE ) ? FALSE : realpath ( "$dir/$src" );

      if ( $path === FALSE or ! str_starts_with ( $path, $dir . DIRECTORY_SEPARATOR ) or ! is_file ( $path ) )
        return NULL;

      $data = file_get_contents ( $path );

    }

    if ( ! is_string ( $data ) or $data === '' )
      return NULL;

    $info = @getimagesizefromstring ( $data );

    if ( ! $info or $info [2] != IMAGETYPE_JPEG )
      return NULL;

    return [ $data, $info [0], $info [1], $info ['channels'] ?? 3 ];

  }

  // ------------------------------------------------------------------------------------
  // Pages, room and spacing.
  // ------------------------------------------------------------------------------------

  function padPdfPage ( &$doc ) {

    $doc ['pages'] [] = [ 'ops' => [], 'links' => [] ];
    $doc ['y']        = $doc ['h'] - $doc ['margin'];
    $doc ['top']      = TRUE;
    $doc ['gap']      = 0;

  }

  // Room for $height below the cursor - with the space a block asked for before it - or a
  // new page; TRUE when a page began.

  function padPdfRoom ( &$doc, $height ) {

    $gap = $doc ['top'] ? 0 : $doc ['gap'];

    if ( ! $doc ['top'] and $doc ['y'] - $gap - $height < $doc ['margin'] ) {
      padPdfPage ( $doc );
      $gap = 0;
    }

    $doc ['y']  -= $gap;
    $doc ['gap'] = 0;
    $doc ['top'] = FALSE;

    return $doc ['y'] == $doc ['h'] - $doc ['margin'];

  }

  // The space between blocks: the larger of what the one above leaves and what the next
  // asks for, as CSS collapses margins; none at the top of a page.

  function padPdfSpace ( &$doc, $before ) {

    $doc ['gap'] = max ( $doc ['gap'], $before );

  }

  function padPdfAfter ( &$doc, $after ) {

    $doc ['gap'] = max ( $doc ['gap'], $after );

  }

  function padPdfOps ( &$doc, $ops ) {

    $doc ['pages'] [ count ( $doc ['pages'] ) - 1 ] ['ops'] [] = $ops;

  }

  // "2 / 5" under every page of a document of more than one.

  function padPdfNumbers ( &$doc ) {

    $count = count ( $doc ['pages'] );

    if ( $count < 2 )
      return;

    $style = padPdfContext ();
    $style ['size']  = 8;
    $style ['color'] = '0.45 0.45 0.45';
    $font  = padPdfFont ( $doc, $style );

    foreach ( $doc ['pages'] as $k => $page ) {
      $text = ( $k + 1 ) . " / $count";
      $x    = ( $doc ['w'] - padPdfWidth ( $doc, $text, $style ) ) / 2;
      $doc ['pages'] [$k] ['ops'] [] = "BT {$style ['color']} rg /$font 8 Tf " . padPdfN ( $x ) . ' ' . padPdfN ( $doc ['margin'] / 2 ) . " Td ($text) Tj ET";
    }

  }

  // ------------------------------------------------------------------------------------
  // Fonts and text.
  // ------------------------------------------------------------------------------------

  // The resource name of the font a style is set in - F1, F2 ... in the order of first use.

  function padPdfFont ( &$doc, $style ) {

    $base = padPdfBaseFont ( $doc, $style );

    if ( ! isset ( $doc ['fonts'] [$base] ) )
      $doc ['fonts'] [$base] = 'F' . ( count ( $doc ['fonts'] ) + 1 );

    return $doc ['fonts'] [$base];

  }

  function padPdfBaseFont ( $doc, $style ) {

    $family = $style ['mono'] ? 'courier' : $doc ['family'];

    return PAD_PDF_FONTS [$family] [ ( $style ['bold'] ? 1 : 0 ) + ( $style ['italic'] ? 2 : 0 ) ];

  }

  // The width in points of WinAnsi text in a style.

  function padPdfWidth ( $doc, $text, $style ) {

    static $tables = [];

    $base = padPdfBaseFont ( $doc, $style );

    if ( str_starts_with ( $base, 'Courier' ) )
      return strlen ( $text ) * 0.6 * $style ['size'];

    $name = strtr ( $base, [ 'Helvetica-Oblique' => 'Helvetica', 'Helvetica-BoldOblique' => 'Helvetica-Bold' ] );

    if ( ! isset ( $tables [$name] ) )
      $tables [$name] = array_map ( 'intval', explode ( ' ', PAD_PDF_WIDTHS [$name] ) );

    $sum = 0;

    foreach ( count_chars ( $text, 1 ) as $byte => $times )
      $sum += ( $byte >= 32 ? $tables [$name] [ $byte - 32 ] : 0 ) * $times;

    return $sum * $style ['size'] / 1000;

  }

  // UTF-8 to WinAnsi: what the encoding has, and a ? for what it has not. The spaces of
  // typography become a space, a hyphen that only may break is left out.

  function padPdfText ( $text ) {

    $text = strtr ( $text, [ "\u{2009}" => ' ', "\u{200A}" => ' ', "\u{202F}" => ' ', "\u{2002}" => ' ', "\u{2003}" => ' ',
                             "\u{00AD}" => '', "\u{200B}" => '', "\u{2212}" => '-', "\u{2011}" => '-', "\u{2010}" => '-' ] );

    $text = mb_convert_encoding ( $text, 'Windows-1252', 'UTF-8' );

    return preg_replace ( '/[\x00-\x1F\x7F]/', '', $text );

  }

  function padPdfString ( $text ) {

    return strtr ( $text, [ '\\' => '\\\\', '(' => '\\(', ')' => '\\)' ] );

  }

  function padPdfN ( $n ) {

    $text = rtrim ( rtrim ( number_format ( $n, 2, '.', '' ), '0' ), '.' );

    return ( $text === '-0' ) ? '0' : $text;

  }

  // ------------------------------------------------------------------------------------
  // The file: objects, the cross-reference table with the offset of each, the trailer.
  // ------------------------------------------------------------------------------------

  function padPdfWrite ( $doc, $compress ) {

    $objects = [];
    $add     = function ( $body ) use ( &$objects ) {
      $objects [] = $body;
      return count ( $objects );
    };

    $catalog = $add ( '' );
    $tree    = $add ( '' );
    $title   = $doc ['title'] !== '' ? $doc ['title'] : $doc ['heading'];
    $info    = $add ( '<< /Producer (PAD)' . ( $title !== '' ? ' /Title ' . padPdfInfoString ( $title ) : '' ) . ' >>' );

    $fonts = '';
    foreach ( $doc ['fonts'] as $base => $name )
      $fonts .= "/$name " . $add ( "<< /Type /Font /Subtype /Type1 /BaseFont /$base /Encoding /WinAnsiEncoding >>" ) . ' 0 R ';

    $images = '';
    foreach ( $doc ['images'] as $image ) {
      $space = [ 1 => '/DeviceGray', 3 => '/DeviceRGB', 4 => '/DeviceCMYK /Decode [1 0 1 0 1 0 1 0]' ] [ $image ['channels'] ] ?? '/DeviceRGB';
      $images .= "/{$image ['name']} " . $add ( "<< /Type /XObject /Subtype /Image /Width {$image ['w']} /Height {$image ['h']}"
                                             . " /ColorSpace $space /BitsPerComponent 8 /Filter /DCTDecode /Length " . strlen ( $image ['data'] )
                                             . " >>\nstream\n{$image ['data']}\nendstream" ) . ' 0 R ';
    }

    $resources = '<< /ProcSet [/PDF /Text /ImageC /ImageB] /Font << ' . $fonts . '>>' . ( $images !== '' ? " /XObject << $images>>" : '' ) . ' >>';

    $kids = [];

    foreach ( $doc ['pages'] as $page ) {

      $ops = implode ( "\n", $page ['ops'] );

      if ( $compress ) {
        $ops    = gzcompress ( $ops, 6 );
        $stream = $add ( '<< /Filter /FlateDecode /Length ' . strlen ( $ops ) . " >>\nstream\n$ops\nendstream" );
      } else
        $stream = $add ( '<< /Length ' . strlen ( $ops ) . " >>\nstream\n$ops\nendstream" );

      $annots = [];
      foreach ( $page ['links'] as list ( $x0, $y0, $x1, $y1, $uri ) )
        $annots [] = $add ( '<< /Type /Annot /Subtype /Link /Rect [' . padPdfN ( $x0 ) . ' ' . padPdfN ( $y0 ) . ' ' . padPdfN ( $x1 ) . ' ' . padPdfN ( $y1 ) . ']'
                          . ' /Border [0 0 0] /A << /S /URI /URI (' . padPdfString ( preg_replace ( '/[^\x21-\x7E]/', '', $uri ) ) . ') >> >>' ) . ' 0 R';

      $kids [] = $add ( "<< /Type /Page /Parent $tree 0 R /MediaBox [0 0 " . padPdfN ( $doc ['w'] ) . ' ' . padPdfN ( $doc ['h'] ) . ']'
                      . " /Resources $resources /Contents $stream 0 R" . ( $annots ? ' /Annots [' . implode ( ' ', $annots ) . ']' : '' ) . ' >>' ) . ' 0 R';

    }

    $objects [ $catalog - 1 ] = "<< /Type /Catalog /Pages $tree 0 R >>";
    $objects [ $tree - 1 ]    = '<< /Type /Pages /Kids [' . implode ( ' ', $kids ) . '] /Count ' . count ( $kids ) . ' >>';

    $pdf     = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
    $offsets = [];

    foreach ( $objects as $k => $body ) {
      $offsets [] = strlen ( $pdf );
      $pdf .= ( $k + 1 ) . " 0 obj\n$body\nendobj\n";
    }

    $xref = strlen ( $pdf );
    $size = count ( $objects ) + 1;
    $id   = md5 ( $pdf );

    $pdf .= "xref\n0 $size\n0000000000 65535 f \n";

    foreach ( $offsets as $offset )
      $pdf .= sprintf ( "%010d 00000 n \n", $offset );

    return $pdf . "trailer\n<< /Size $size /Root $catalog 0 R /Info $info 0 R /ID [<$id> <$id>] >>\nstartxref\n$xref\n%%EOF\n";

  }

  // A text of the document's information: plain ASCII as it is, anything else as UTF-16
  // with its byte order mark, which every reader shows.

  function padPdfInfoString ( $text ) {

    if ( preg_match ( '/^[\x20-\x7E]*$/D', $text ) )
      return '(' . padPdfString ( $text ) . ')';

    return '<FEFF' . strtoupper ( bin2hex ( mb_convert_encoding ( $text, 'UTF-16BE', 'UTF-8' ) ) ) . '>';

  }

?>
