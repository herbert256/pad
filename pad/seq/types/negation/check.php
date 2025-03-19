<?php

  function padSeqCheckNegation ( $f, $n ) {

    if ( file_exists ( 'seq/types/negation/bool.php' ) )
      return padSeqBoolNegation ( $n );

    if ( file_exists ( 'seq/types/negation/generated.php' ) ) 
      return in_array ( $n, PADnegation );

    if ( file_exists ( 'seq/types/negation/fixed.php' ) ) {
      $fixed = include 'seq/types/negation/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence negation, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>