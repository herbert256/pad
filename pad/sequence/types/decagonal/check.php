<?php

  function padSeqCheckDecagonal ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/decagonal/bool.php' ) )
      return padSeqBoolDecagonal ( $n, $p );

    if ( file_exists ( 'sequence/types/decagonal/fixed.php' ) ) {
      $fixed = include 'sequence/types/decagonal/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/decagonal/generated.php' ) ) 
      return in_array ( $n, PADdecagonal );

    $text = padCode ( "{sequence decagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>