<?php

  function padSeqCheckHappy ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/happy/bool.php' ) )
      return padSeqBoolHappy ( $n, $p );

    if ( file_exists ( 'sequence/types/happy/generated.php' ) ) 
      return in_array ( $n, PADhappy );

    if ( file_exists ( 'sequence/types/happy/fixed.php' ) ) {
      $fixed = include 'sequence/types/happy/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence happy, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>