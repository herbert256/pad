<?php

  function padSeqCheckHappy ( $f, $n ) {

    if ( file_exists ( 'seq/types/happy/bool.php' ) )
      return padSeqBoolHappy ( $n );

    if ( file_exists ( 'seq/types/happy/generated.php' ) ) 
      return in_array ( $n, PADhappy );

    if ( file_exists ( 'seq/types/happy/fixed.php' ) ) {
      $fixed = include 'seq/types/happy/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence happy, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>