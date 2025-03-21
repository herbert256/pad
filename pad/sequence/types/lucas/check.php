<?php

  function padSeqCheckLucas ( $f, $n ) {

    if ( file_exists ( 'sequence/types/lucas/bool.php' ) )
      return padSeqBoolLucas ( $n );

    if ( file_exists ( 'sequence/types/lucas/generated.php' ) ) 
      return in_array ( $n, PADlucas );

    if ( file_exists ( 'sequence/types/lucas/fixed.php' ) ) {
      $fixed = include 'sequence/types/lucas/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence lucas, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>