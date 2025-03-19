<?php

  function padSeqCheckOctagonal ( $f, $n ) {

    if ( file_exists ( 'seq/types/octagonal/bool.php' ) )
      return padSeqBoolOctagonal ( $n );

    if ( file_exists ( 'seq/types/octagonal/generated.php' ) ) 
      return in_array ( $n, PADoctagonal );

    if ( file_exists ( 'seq/types/octagonal/fixed.php' ) ) {
      $fixed = include 'seq/types/octagonal/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence octagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>