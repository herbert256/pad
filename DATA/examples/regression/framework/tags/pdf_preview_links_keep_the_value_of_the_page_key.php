<?php

  // The Open and Download links of a {pdf preview} keep the value of the page's own key:
  // ?invoice=114 names the page invoice and hands it $invoice = 114, and its links asked
  // ?invoice&padPdf= - the document of no invoice. A bare key stays out, as before.

  $saved = [ $_GET, $padPageAsked ];

  $open = function ( $get ) {
    global $padPageAsked;
    $_GET         = $get;
    $padPageAsked = 'invoice';
    preg_match ( '/href="[^"?]*([^"]*)"/', padPdfLinks ( 'invoice' ), $found );
    return html_entity_decode ( $found [1] );
  };

  $valued = $open ( [ 'invoice' => '114', 'lang' => 'nl' ] );
  $bare   = $open ( [ 'invoice' => '', 'id' => '7' ] );

  [ $_GET, $padPageAsked ] = $saved;

?>
