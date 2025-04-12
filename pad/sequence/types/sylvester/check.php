<?php

  function padSeqCheckSylvester ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/sylvester/bool.php' ) )
      return padSeqBoolSylvester ( $n, $p );

    if ( file_exists ( 'sequence/types/sylvester/generated.php' ) ) 
      return in_array ( $n, PADsylvester );

    if ( file_exists ( 'sequence/types/sylvester/fixed.php' ) ) {
      $fixed = include 'sequence/types/sylvester/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence sylvester, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>