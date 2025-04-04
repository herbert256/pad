<?php

  function padSeqCheckTetrahedral ( $f, $n ) {

    if ( file_exists ( 'sequence/types/tetrahedral/bool.php' ) )
      return padSeqBoolTetrahedral ( $n );

    if ( file_exists ( 'sequence/types/tetrahedral/generated.php' ) ) 
      return in_array ( $n, PADtetrahedral );

    if ( file_exists ( 'sequence/types/tetrahedral/fixed.php' ) ) {
      $fixed = include 'sequence/types/tetrahedral/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence tetrahedral, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>