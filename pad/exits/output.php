<?php

  $padLen = ( $padStop == 200 ) ? strlen ( $padOutput ) : 0;

  padCheckBuffers ();

  if ( $padOutputType <> 'web' and $padCacheStop == 200 and $padCacheServerGzip )
    $padOutput = padUnzip ( $padOutput );

  include PAD . "exits/output/$padOutputType.php";

  padExit ( $padStop );

?>