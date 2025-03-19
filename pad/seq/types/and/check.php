<?php

  function padSeqCheckAnd ( $f, $n ) {

    if ( file_exists ( 'seq/types/and/bool.php' ) )
      return padSeqBoolAnd ( $n );

    if ( file_exists ( 'seq/types/and/generated.php' ) ) 
      return in_array ( $n, PADand );

    if ( file_exists ( 'seq/types/and/fixed.php' ) ) {
      $fixed = include 'seq/types/and/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence and, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>