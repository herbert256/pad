<?php

  function padSeqCheckPerfect ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/perfect/bool.php' ) )
      return padSeqBoolPerfect ( $n );

    if ( file_exists ( PAD . 'seq/types/perfect/generated.php' ) ) 
      return in_array ( $n, PADperfect );

    if ( file_exists ( PAD . 'seq/types/perfect/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/perfect/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq perfect, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>