<?php

  // Fetches the data page as other origins would, and shows the CORS headers of each answer:
  // the listed origin gets itself back, with credentials, the exposed header and Vary:
  // Origin; another origin gets no Access-Control header at all; a preflight from the
  // listed origin is a 204 with the methods, headers and age, one from another a bare 204 -
  // neither runs the page nor asks the CSRF token; a post from the listed origin without
  // its token is still refused, its CORS headers on the 403; with every origin allowed the
  // answer is * and depends on no Origin.

  $base   = $padHost . 'regression/cors/';
  $probes = [];

  $asks = [
    'listed'          => [ 'url' => $base . '?data&padInclude', 'headers' => [ 'Origin' => 'https://app.example.com' ] ],
    'other'           => [ 'url' => $base . '?data&padInclude', 'headers' => [ 'Origin' => 'https://evil.example.org' ] ],
    'none'            => [ 'url' => $base . '?data&padInclude' ],
    'preflight'       => [ 'url' => $base . '?data', 'options' => [ 'CUSTOMREQUEST' => 'OPTIONS' ],
                           'headers' => [ 'Origin' => 'https://app.example.com', 'Access-Control-Request-Method' => 'POST',
                                          'Access-Control-Request-Headers' => 'content-type' ] ],
    'preflight other' => [ 'url' => $base . '?data', 'options' => [ 'CUSTOMREQUEST' => 'OPTIONS' ],
                           'headers' => [ 'Origin' => 'https://evil.example.org', 'Access-Control-Request-Method' => 'POST' ] ],
    'post'            => [ 'url' => $base . '?data', 'post' => [ 'x' => '1' ],
                           'headers' => [ 'Origin' => 'https://app.example.com' ] ],
    'any'             => [ 'url' => $base . '?data&any&padInclude', 'headers' => [ 'Origin' => 'https://elsewhere.example.net' ] ],
  ];

  foreach ( $asks as $name => $ask ) {

    $curl = padCurl ( $ask );

    $cors = [];

    foreach ( $curl ['headers'] as $header => $value )
      if ( str_starts_with ( strtolower ( $header ), 'access-control-' ) or strtolower ( $header ) == 'vary' )
        $cors [] = "$header: $value";

    sort ( $cors );

    $probes [] = [
      'name'   => $name,
      'status' => $curl ['result'],
      'cors'   => $cors ? implode ( "\n", $cors ) : '(none)',
      'body'   => strlen ( trim ( $curl ['data'] ) ) > 60 ? substr ( trim ( $curl ['data'] ), 0, 60 ) . '...' : trim ( $curl ['data'] )
    ];

  }

?>
