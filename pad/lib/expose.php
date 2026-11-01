<?php

  // Data answers: the page that renders orders.pad answering with its data instead, as JSON
  // or as CSV - what a React component, a script or a spreadsheet wants from the same page.
  //
  //   ?orders&padFormat=json             asked for in the URL (padFormat=csv for CSV)
  //   Accept: application/json           asked for by content negotiation (or text/csv)
  //   $padOutputType = 'json'            every page of the application, from its config
  //
  // What may leave the server is an explicit list the page's PHP makes - $padExpose =
  // [ 'orders', 'total' ] in orders.php, or in an _inits.php for a whole directory - and
  // nothing else does: never every variable, never an engine name. A page that names none
  // answers HTML as it always did; asked for data outright, it answers 406. Once the page's
  // PHP has run, build/expose.php decides, and a data answer skips the templates entirely;
  // the writers in exits/output/json.php and csv.php encode the named variables and the web
  // writer sends them with their own content type, ETag and 304, like any page.
  //
  // padClientFormat   what the request asks for: an explicit padFormat as written, or else
  //                   the format the Accept header asks for, '' when not
  // padAcceptFormat   the Accept header read with its quality values - a header that accepts
  //                   text/html, by name or through a wildcard as high as the format, asks
  //                   for nothing, so a browser, a feed reader or an agent gets the page
  // padExposeRefuse   the 406 answer for a format this page cannot give
  //
  // The bodies of a data answer - the exposed variables, the JSON, the CSV - are
  // build/_lib/expose.php, read when a request is answered with data.
  //
  // Every other format of the formats subsystem that writes answers the same way -
  // ?events&padFormat=ics, Accept: text/calendar, padFormat=xlsx, padFormat=rss - through the
  // output type 'format', whose format is $padOutputFormat: a table format (ics, xlsx, the
  // feeds ...) the first exposed list as CSV has it, any other (toml, yaml) the exposed
  // variables by name as JSON has them, with the format's media type, a file name where it
  // has one, and the page's $padFormatOptions for its writer - a feed's title and link.

  function padClientFormat () {

    if ( isset ( $_REQUEST ['padFormat'] ) )
      return is_string ( $_REQUEST ['padFormat'] ) ? strtolower ( trim ( $_REQUEST ['padFormat'] ) ) : '?';

    return padAcceptFormat ( $_SERVER ['HTTP_ACCEPT'] ?? '' );

  }

  // A page that renders HTML answers HTML to every header that accepts it: data is chosen by
  // the Accept header only where the header does not accept text/html - names neither it nor
  // application/xhtml+xml with a quality above 0 - and no wildcard takes it at the format's
  // quality or above: */*, or text/*, which is text/html too. So a browser, a feed reader or
  // an agent that sends text/html beside what it prefers - text/markdown, text/html;q=0.9 -
  // gets the page, as does axios' application/json, text/plain, */*; while application/json,
  // jQuery's application/json, */*;q=0.01, text/calendar or a feed reader's
  // application/rss+xml, */*;q=0.8 get the data, where the page has it to give.
  //
  // The format is the media type the header prefers, of those above the wildcards: JSON and
  // CSV by name, any other a format of the formats subsystem (lib/format.php) that writes
  // and is asked for by that type (padFormatAccepted) - a header that names text/html loads
  // no format. padFormat= in the query is the explicit way, for any header.

  function padAcceptFormat ( $header ) {

    $named = [];
    $any   = -1.0;

    foreach ( explode ( ',', $header ) as $one ) {

      $parts = explode ( ';', $one );
      $name  = strtolower ( trim ( $parts [0] ) );
      $q     = 1.0;

      foreach ( array_slice ( $parts, 1 ) as $parm )
        if ( preg_match ( '/^\s*q\s*=\s*([0-9.]+)\s*$/i', $parm, $match ) )
          $q = (float) $match [1];

      if     ( $name == 'text/html' or $name == 'application/xhtml+xml' ) { if ( $q > 0 ) return ''; }
      elseif ( $name == 'text/*' or $name == '*/*'                      ) $any = max ( $any, $q );
      elseif ( $name !== ''                                             ) $named [$name] = max ( $named [$name] ?? -1.0, $q );

    }

    $named = array_filter ( $named, fn ( $q ) => $q > 0 and $q > $any );

    arsort ( $named );

    foreach ( $named as $type => $q ) {

      $kind = match ( $type ) { 'application/json' => 'json', 'text/csv' => 'csv', default => padFormatAccepted ( $type ) };

      if ( $kind !== '' )
        return $kind;

    }

    return '';

  }

  // The format that writes, and is asked for by the media type - or ''.

  function padFormatAccepted ( $type ) {

    foreach ( padFormatKinds () as $kind ) {
      $def = padFormatDef ( $kind );
      if ( isset ( $def ['write'] ) and in_array ( $type, $def ['accept'] ?? [ $def ['mime'] ?? '' ], TRUE ) )
        return $kind;
    }

    return '';

  }

  // The format a page answers in for the format the request asks for: json and csv as
  // before, any other a format of the formats subsystem that writes, by its name or a file
  // extension of it - or '' for none.

  function padExposeKind ( $format ) {

    if ( $format === 'json' or $format === 'csv' )
      return $format;

    if ( $format === '' or $format === 'html' or ! is_string ( $format ) )
      return '';

    return padFormatFind ( $format, 'write' );

  }

  // Asked for a format the page cannot give: 406, what went wrong told to this machine's
  // own requests only - the name of the page and of the setting are the author's business.

  function padExposeRefuse ( $why ) {

    while ( ob_get_level () )
      ob_end_clean ();

    if ( ! headers_sent () ) {
      http_response_code ( 406 );
      header ( 'Content-Type: text/plain; charset=UTF-8' );
      header ( 'Vary: Accept' );
    }

    echo padLocal () ? $why : 'Not acceptable';

    $stop = 406;
    include PAD . 'exits/exit.php';

  }

?>
