<?php

  function padSeqCheckEmirp ( $f, $n ) {

    if ( file_exists ( 'seq/types/emirp/bool.php' ) )
      return padSeqBoolEmirp ( $n );

    if ( file_exists ( 'seq/types/emirp/generated.php' ) ) 
      return in_array ( $n, PADemirp );

    if ( file_exists ( 'seq/types/emirp/fixed.php' ) ) {
      $fixed = include 'seq/types/emirp/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence emirp, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>