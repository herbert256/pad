<?php

  // A bare query key whose first segment a bracketed directory binds and that goes on -
  // ?nl/about under [lang]/about.pad, the relative link a page of a language-prefixed site
  // writes - names that page on a clean URL; only a single segment the root binds - ?nl,
  // which [slug].pad takes - stays a value of the path's page. The refusal of every
  // bracketed match kept the visitor on products/42.

  function langRoute ( $path ) {

    global $padHost;

    $r = padCurl ( $padHost . "regression/clean_urls/index.php/$path" );

    return $r ['result'] == 200 ? trim ( $r ['data'] ) : $r ['result'];

  }

  $langRoute = implode ( ' | ', [ langRoute ( 'products/42?nl/about&padInclude' ), langRoute ( 'products/42?nl&padInclude' ),
                                  langRoute ( 'en/about?padInclude' ) ] );

?>
