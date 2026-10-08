<?php

  // {pdf file='invoice-114'} ... {/pdf} - what the content renders, sent as a PDF instead of
  // the page (lib/pdf.php): a pure PHP writer, the standard fonts, the HTML of a document -
  // headings, paragraphs, lists, tables, JPEG pictures. file= names the download (else the
  // page), download sends it as an attachment instead of showing it, size= A4 (default),
  // Letter, A5 or Legal, landscape turns the page, font= helvetica (default), times or
  // courier, title= the document's title (else its first heading). Written as a single tag,
  // {pdf download}, the whole page is the document.
  //
  // preview keeps the page a page: the content is shown on a sheet with links to the
  // document - the same page asked with &padPdf (padPdf=download for the attachment)
  // answers the PDF. Inside a {page} - a document shown in another page, a card that links
  // on its own - the content is the sheet alone and the response stays the page's: only the
  // page the request asked for answers a document.
  //
  // Like {live} a pair runs twice: first to ask for the end walk, then on the rendered
  // content.

  $padPdfOpts = [ 'file'      => (string) padTagParm ( 'file',      ''          ),
                  'download'  => (bool)   padTagParm ( 'download',  FALSE       ),
                  'size'      => (string) padTagParm ( 'size',      'A4'        ),
                  'landscape' => (bool)   padTagParm ( 'landscape', FALSE       ),
                  'font'      => (string) padTagParm ( 'font',      'helvetica' ),
                  'title'     => (string) padTagParm ( 'title',     ''          ) ];

  $padPdfNested  = (bool) ( $padGuardNested ?? FALSE );
  $padPdfPreview = (bool) padTagParm ( 'preview', FALSE );
  $padPdfAsked   = ( ! $padPdfNested and ( ! $padPdfPreview or isset ( $_GET ['padPdf'] ) ) );

  if ( $padPdfPreview and ( $_GET ['padPdf'] ?? '' ) === 'download' )
    $padPdfOpts ['download'] = TRUE;

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! isset ( PAD_PDF_SIZES [ strtolower ( $padPdfOpts ['size'] ) ] ) and $padCheckSyntax )
      padError ( "the pdf tag has no page size '" . padMakeSafe ( $padPdfOpts ['size'], 20 ) . "' - A4, Letter, A5 or Legal" );

    if ( ! isset ( PAD_PDF_FONTS [ strtolower ( $padPdfOpts ['font'] ) ] ) and $padCheckSyntax )
      padError ( "the pdf tag has no font '" . padMakeSafe ( $padPdfOpts ['font'], 20 ) . "' - helvetica, times or courier" );

    if ( $padPair [$pad] ) {
      $padWalk [$pad] = 'end';
      return TRUE;
    }

    if ( $padPdfAsked )
      padPdfAsk ( NULL, $padPdfOpts );

    return ( $padPdfPreview and ! $padPdfNested ) ? padPdfLinks ( $padPdfOpts ['file'] ) : '';

  }

  if ( $padPdfAsked )
    padPdfAsk ( $padContent, $padPdfOpts );
  else
    $padContent = padPdfSheet ( $padContent, $padPdfNested ? '' : $padPdfOpts ['file'], $padPdfNested );

  return TRUE;

?>
