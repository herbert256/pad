<?php

  // A clean URL keeps a segment's encoded & as part of the segment: the path was decoded
  // before its &name=value tail was split off, so products/a%26b reached products/[id] as
  // a with a value b - and {get}, {ajax} and padRedirect (), which encode a routed segment,
  // had the encoding undone, a visitor's a%26padStats becoming a switch of the request this
  // server makes to itself. A real & still starts the tail. Every path is asked in the
  // index.php/... form, which every server runs without being told (the README): a bare
  // products/... needs FallbackResource, which this machine's Apache does not give.

  function encodedGet ( $path ) {

    global $padHost;

    $r = padCurl ( $padHost . "regression/clean_urls/$path" );

    return $r ['result'] == 200 ? trim ( $r ['data'] ) : $r ['result'];

  }

  $encodedResult = implode ( ' | ', [ encodedGet ( 'index.php/products/a%26b?padInclude' ),
                                      encodedGet ( 'index.php/products/e%26f&padInclude' ),
                                      encodedGet ( 'index.php/products/i&padInclude' ) ] );

?>
