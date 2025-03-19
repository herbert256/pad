<?php

  function padSeqCheckRange ( $f, $n ) {

    if ( file_exists ( 'seq/types/range/bool.php' ) )
      return padSeqBoolRange ( $n );

    if ( file_exists ( 'seq/types/range/generated.php' ) ) 
      return in_array ( $n, PADrange );

    if ( file_exists ( 'seq/types/range/fixed.php' ) ) {
      $fixed = include 'seq/types/range/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence range, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>