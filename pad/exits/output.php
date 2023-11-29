<?php
  
  $padLen = ( $padStop == 200 ) ? strlen ( $padOutput ) : 0;

  padCheckBuffers ();

  if ( $padOutputType <> 'web' and $padCacheStop == 200 and $padCacheServerGzip )
    $padOutput = padUnzip ( $padOutput );

  include pad . "exits/output/$padOutputType.php";

  padStop ( $padStop );

?>