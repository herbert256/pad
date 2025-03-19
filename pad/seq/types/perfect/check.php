<?php

  function padSeqCheckPerfect ( $f, $n ) {

    if ( file_exists ( 'seq/types/perfect/bool.php' ) )
      return padSeqBoolPerfect ( $n );

    if ( file_exists ( 'seq/types/perfect/generated.php' ) ) 
      return in_array ( $n, PADperfect );

    if ( file_exists ( 'seq/types/perfect/fixed.php' ) ) {
      $fixed = include 'seq/types/perfect/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence perfect, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>