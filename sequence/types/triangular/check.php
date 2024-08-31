<?php

  function padSeqCheckTriangular ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/triangular/bool.php' ) )
      return padSeqBoolTriangular ( $n );

    if ( file_exists ( '/pad/sequence/types/triangular/generated.php' ) ) 
      return in_array ( $n, PADtriangular );

    if ( file_exists ( '/pad/sequence/types/triangular/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/triangular/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence triangular, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>