<?php

  // The data answers of format/orders, one line each - in one test, so the suite holds one
  // worker while it asks the server for the rest:
  //
  //   json     ?page&padFormat=json - the exposed variables as one JSON object, with the JSON
  //            content type; the template does not run
  //   csv      ?page&padFormat=csv - the first exposed list, a header row and a line per
  //            row, quoted where a value holds a comma, named after the page
  //   accept   an Accept header that prefers JSON or CSV gets data, a browser's gets the page,
  //            and every answer of an exposing page says Vary: Accept; a page that exposes
  //            nothing - format/host, which embeds one that does - renders its HTML
  //   refused  asked outright for what the page cannot give: 406 for a page exposing nothing,
  //            for a format that does not exist and for CSV without a list; JSON carries a
  //            single value

  function formatAsk ( $page, $accept = '' ) {

    global $padGoExt;

    $input = [ 'url' => $padGoExt . $page ];

    if ( $accept )
      $input ['headers'] = [ 'Accept' => $accept ];

    return padCurl ( $input );

  }

  function formatType ( $page, $accept ) {

    $r = formatAsk ( "$page&padInclude", $accept );

    return explode ( ';', $r ['headers'] ['Content-Type'] ?? '' ) [0] . ' ' . ( $r ['headers'] ['Vary'] ?? '-' );

  }

  $j = formatAsk ( 'format/orders&padFormat=json' );
  $c = formatAsk ( 'format/orders&padFormat=csv' );

  echo 'json: ', $j ['result'], ' ', $j ['headers'] ['Content-Type'] ?? '', ' ', json_encode ( json_decode ( $j ['data'] ) ), "\n";

  echo 'csv: ', $c ['result'], ' ', $c ['headers'] ['Content-Type'] ?? '', ' ', $c ['headers'] ['Content-Disposition'] ?? '', ' | ',
       str_replace ( "\n", ' | ', trim ( $c ['data'] ) ), "\n";

  echo 'accept: ', implode ( ' / ', [
         formatType ( 'format/orders', 'application/json' ),
         formatType ( 'format/orders', 'text/csv' ),
         formatType ( 'format/orders', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' ),
         formatType ( 'format/orders', 'text/html;q=0.5, application/json' ),
         formatType ( 'format/orders', '*/*' ),
         formatType ( 'format/host',   'application/json' ) ] ), "\n";

  $d = formatAsk ( 'format/scalar&padFormat=json' );

  echo 'refused: ', implode ( ' ', [
         formatAsk ( 'format/host&padFormat=json'  ) ['result'],
         formatAsk ( 'format/orders&padFormat=xml' ) ['result'],
         formatAsk ( 'format/scalar&padFormat=csv' ) ['result'],
         $d ['result'] ] ), ' ', json_encode ( json_decode ( $d ['data'] ) );

?>
