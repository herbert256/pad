<?php

  // A route at the root, [slug].pad, binds any name - and the bare query key a clean URL
  // carries is a value of the page its path names, not a slug: index.php/products/42?padInclude
  // rendered [slug] with $slug = padInclude, the &padInclude tail of a {$padGo}page&padInclude
  // link the same, and ?back on a product page never reached it. A key whose first segment
  // is a page or a directory of its own - ?products/7 - still names that page.

  function rootRoute ( $path ) {

    global $padHost;

    $r = padCurl ( $padHost . "regression/clean_urls/index.php/$path" );

    return $r ['result'] == 200 ? trim ( $r ['data'] ) : $r ['result'];

  }

  $rootRoute = implode ( ' | ', [ rootRoute ( 'products/42?padInclude' ), rootRoute ( 'products/42&padInclude' ),
                                  rootRoute ( 'hello-pad?padInclude' ), rootRoute ( 'hello-pad?products/7&padInclude' ) ] );

?>
