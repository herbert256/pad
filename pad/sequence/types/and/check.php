<?php

  function padSeqCheckAnd ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/and/bool.php' ) )
      return padSeqBoolAnd ( $n, $p );

    if ( file_exists ( 'sequence/types/and/generated.php' ) ) 
      return in_array ( $n, PADand );

    if ( file_exists ( 'sequence/types/and/fixed.php' ) ) {
      $fixed = include 'sequence/types/and/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence and='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>