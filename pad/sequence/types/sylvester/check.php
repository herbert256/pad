<?php

  function pqCheckSylvester ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/sylvester/bool.php' ) )
      return pqBoolSylvester ( $n, $p );

    if ( file_exists ( 'sequence/types/sylvester/fixed.php' ) ) {
      $fixed = include 'sequence/types/sylvester/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/sylvester/generated.php' ) ) 
      return in_array ( $n, PADsylvester );

    $text = padCode ( "{sequence sylvester, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>