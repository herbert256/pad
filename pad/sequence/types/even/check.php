<?php

  function padSeqCheckEven ( $f, $n ) {

    if ( file_exists ( 'sequence/types/even/bool.php' ) )
      return padSeqBoolEven ( $n );

    if ( file_exists ( 'sequence/types/even/generated.php' ) ) 
      return in_array ( $n, PADeven );

    if ( file_exists ( 'sequence/types/even/fixed.php' ) ) {
      $fixed = include 'sequence/types/even/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence even, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>