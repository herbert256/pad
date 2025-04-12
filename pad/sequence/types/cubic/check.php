<?php

  function padSeqCheckCubic ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/cubic/bool.php' ) )
      return padSeqBoolCubic ( $n, $p );

    if ( file_exists ( 'sequence/types/cubic/generated.php' ) ) 
      return in_array ( $n, PADcubic );

    if ( file_exists ( 'sequence/types/cubic/fixed.php' ) ) {
      $fixed = include 'sequence/types/cubic/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence cubic, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>