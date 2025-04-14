<?php

  function padSeqCheckSemiprime ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/semiprime/bool.php' ) )
      return padSeqBoolSemiprime ( $n, $p );

    if ( file_exists ( 'sequence/types/semiprime/fixed.php' ) ) {
      $fixed = include 'sequence/types/semiprime/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/semiprime/generated.php' ) ) 
      return in_array ( $n, PADsemiprime );

    $text = padCode ( "{sequence semiprime, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>