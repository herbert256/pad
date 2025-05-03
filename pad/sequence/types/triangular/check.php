<?php

  function pqCheckTriangular ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/triangular/bool.php' ) )
      return pqBoolTriangular ( $n, $p );

    if ( file_exists ( 'sequence/types/triangular/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/triangular/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/triangular/generated.php' ) ) 
      return in_array ( $n, PADtriangular );

    #$text = padCode ( "{sequence triangular, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence triangular, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>