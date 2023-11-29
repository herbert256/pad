<?php
  
  $padLen = ( $padStop == 200 ) ? strlen ( $padOutput ) : 0;

  $padIllegal = padEmptyBuffers ();

  if ( trim ( $padIllegal ) )
    return padError ( "Illegal output: '$padIllegal'" );

  if ( $padOutputType <> 'web' and $padCacheStop and $padCacheServerGzip and $padStop == 200 )
    $padOutput = padUnzip ( $padOutput );

  include pad . "exits/output/$padOutputType.php";

  padStop ( $padStop );

?>