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
  //                   'json' or 'csv' when the Accept header prefers one, '' when not
  // padAcceptFormat   the Accept header read with its quality values - html wins a tie, and
  //                   */* alone asks for nothing, so a browser keeps getting the page
  // padExposeRefuse   the 406 answer for a format this page cannot give
  //
  // The bodies of a data answer - the exposed variables, the JSON, the CSV - are
  // build/_lib/expose.php, read when a request is answered with data.

  function padClientFormat () {

    if ( isset ( $_REQUEST ['padFormat'] ) )
      return is_string ( $_REQUEST ['padFormat'] ) ? strtolower ( trim ( $_REQUEST ['padFormat'] ) ) : '?';

    return padAcceptFormat ( $_SERVER ['HTTP_ACCEPT'] ?? '' );

  }

  // Only a type the header names counts for data: application/json and text/csv must be
  // there by name, with a quality above the one html gets - by name, through text/*, or
  // through */*. Without either, the answer is '' and the page renders as before.

  function padAcceptFormat ( $header ) {

    $named = [ 'html' => -1.0, 'json' => -1.0, 'csv' => -1.0 ];
    $text  = -1.0;
    $any   = -1.0;

    $types = [ 'text/html'             => 'html',
               'application/xhtml+xml' => 'html',
               'application/json'      => 'json',
               'text/csv'              => 'csv' ];

    foreach ( explode ( ',', $header ) as $one ) {

      $parts = explode ( ';', $one );
      $name  = strtolower ( trim ( $parts [0] ) );
      $q     = 1.0;

      foreach ( array_slice ( $parts, 1 ) as $parm )
        if ( preg_match ( '/^\s*q\s*=\s*([0-9.]+)\s*$/i', $parm, $match ) )
          $q = (float) $match [1];

      if     ( isset ( $types [$name] ) ) $named [ $types [$name] ] = max ( $named [ $types [$name] ], $q );
      elseif ( $name == 'text/*'        ) $text = max ( $text, $q );
      elseif ( $name == '*/*'           ) $any  = max ( $any,  $q );

    }

    $html = ( $named ['html'] >= 0 ) ? $named ['html'] : ( ( $text >= 0 ) ? $text : max ( $any, 0.0 ) );
    $best = ( $named ['csv'] > $named ['json'] ) ? 'csv' : 'json';

    if ( $named [$best] > 0 and $named [$best] > $html )
      return $best;

    return '';

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
