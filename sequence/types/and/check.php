<?php

  function padSeqCheckAnd ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/and/bool.php' ) )
      return padSeqBoolAnd ( $n );

    if ( file_exists ( '/pad/sequence/types/and/generated.php' ) ) 
      return in_array ( $n, PADand );

    if ( file_exists ( '/pad/sequence/types/and/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/and/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence and, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>