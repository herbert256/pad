<?php

  // A route asked through the query string binds the segment as the path form does: PHP
  // writes a dot or a space in a query key as _, and ?routes/products/v1.2 bound $id v1_2
  // where index.php/routes/products/v1.2 bound v1.2. The page is read from the query string
  // as it was sent.

  function queryDots ( $query ) {

    global $padHost;

    $r = padCurl ( $padHost . "regression/pages/?$query&padInclude" );

    return $r ['result'] == 200 ? trim ( $r ['data'] ) : $r ['result'];

  }

  $queryDots = implode ( ' | ', [ queryDots ( 'routes/products/v1.2' ), queryDots ( 'routes/products/a%20b' ),
                                  queryDots ( 'routes/products/c+d' ), queryDots ( 'routes/products/42' ) ] );

?>
