<?php

  function padSeqCheckList ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/list/bool.php' ) )
      return padSeqBoolList ( $n, $p );

    if ( file_exists ( 'sequence/types/list/generated.php' ) ) 
      return in_array ( $n, PADlist );

    if ( file_exists ( 'sequence/types/list/fixed.php' ) ) {
      $fixed = include 'sequence/types/list/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence list, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>