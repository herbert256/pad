<?php

  // Decides whether this request is answered with the page's data instead of its template
  // (lib/expose.php). Runs from build/build.php once the PHP chain - _inits.php, the page's
  // own .php, _exits.php - has run, because that is where $padExpose is set; for the
  // request's own page only, which inits/app.php marks - a {page} rendered inside it never
  // turns the response into data.
  //
  // The json and csv output types answer data for every page, in the one of the two the
  // request asks for, if it does. Otherwise a request asking for json or csv gets it when
  // the page exposes something; asked outright with ?padFormat=, a
  // page that exposes nothing, or a format that does not exist, answers 406, while a request
  // that only said so in its Accept header gets the HTML page as always.
  //
  // A data answer switches the output type for the rest of the request and empties the
  // source about to be rendered - _lib snippets, wrappers and page alike - so no template
  // runs; the exit writer then encodes what the page exposes.

  if ( ! ( $padExposeCheck ?? FALSE ) )
    return;

  $padExposeCheck  = FALSE;
  $padExposeFormat = '';

  $padExposeClient = ( $padClientFormat == 'json' or $padClientFormat == 'csv' );

  if ( $padOutputType == 'json' or $padOutputType == 'csv' )

    $padExposeFormat = $padExposeClient ? $padClientFormat : $padOutputType;

  elseif ( $padExposeClient ) {

    if ( count ( (array) $padExpose ) )
      $padExposeFormat = $padClientFormat;
    elseif ( $padClientFormatAsked )
      padExposeRefuse ( "page '$padPage' answers no $padClientFormat - its .php names nothing in \$padExpose" );

  } elseif ( $padClientFormatAsked and $padClientFormat !== 'html' and $padClientFormat !== '' )

    padExposeRefuse ( "there is no format named '" . padMakeSafe ( $padClientFormat, 20 ) . "' - html, json or csv" );

  if ( ! $padExposeFormat )
    return;

  // CSV is a table: a request for it where the page has no list is the visitor's to hear
  // about, where the csv type set in the configuration is the author's (exits/output/csv.php).

  if ( $padExposeFormat == 'csv' and $padExposeClient and padExposeList () === NULL )
    padExposeRefuse ( "page '$padPage' answers no csv - nothing it names in \$padExpose is a list" );

  // The type's settings are loaded unless the configuration pass already applied them -
  // that pass, which the application's config may have refined, stands.

  $padOutputType = $padExposeFormat;

  if ( $padOutputType != ( $padConfigSet ['outputType'] ?? $padConfigDefault ['outputType'] ) )
    include PAD . "config/output/$padOutputType.php";

  $padBuildLib  = '';
  $padBuildBase = '@page@';
  $padBuildPage = '';

?>