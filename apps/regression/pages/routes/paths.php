<?php

  // Clean URLs, in the form every server runs without being told: the path behind the entry
  // point, index.php/routes/products/42, is mapped onto the file tree - a literal name before
  // a bracket, [id] binding one segment, [path+] the rest - and the query string still names
  // the page when it starts with a bare page name. ?id=7 does not replace the $id the path
  // bound, and a path that matches nothing is not found. The second line asks the same
  // routes through the query string, for a server that routes no paths. One test, so the
  // suite holds one worker while it asks the server for the rest.

  function routesGet ( $path ) {

    global $padHost;

    $r = padCurl ( $padHost . "regression/pages/index.php/$path" );

    return $r ['result'] == 200 ? trim ( $r ['data'] ) : $r ['result'];

  }

  echo implode ( ' | ', [
    routesGet ( 'routes/products/42?padInclude' ),
    routesGet ( 'routes/products/new?padInclude' ),
    routesGet ( 'routes/products?padInclude' ),
    routesGet ( 'routes/blog/2026/hello-pad&padInclude' ),
    routesGet ( 'routes/docs/guide/install.html?padInclude' ),
    routesGet ( 'routes/products/42?padInclude&id=7' ),
    routesGet ( 'routes/products/42?routes/products/new&padInclude' ),
    routesGet ( 'routes/nothing/here?padInclude' ),
    routesGet ( 'routes/products/_hidden?padInclude' )
  ] ), "\n";

  echo trim ( padCurl ( $padGoExt . 'routes/products/43&padInclude'      ) ['data'] ), ' | ',
       trim ( padCurl ( $padGoExt . 'routes/blog/2027/second&padInclude' ) ['data'] );

?>
