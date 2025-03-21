<?php

  function padSeqCheckDivide ( $f, $n ) {

    if ( file_exists ( 'sequence/types/divide/bool.php' ) )
      return padSeqBoolDivide ( $n );

    if ( file_exists ( 'sequence/types/divide/generated.php' ) ) 
      return in_array ( $n, PADdivide );

    if ( file_exists ( 'sequence/types/divide/fixed.php' ) ) {
      $fixed = include 'sequence/types/divide/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence divide, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>