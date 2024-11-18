<?php

  function padSeqCheckTetrahedral ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/tetrahedral/bool.php' ) )
      return padSeqBoolTetrahedral ( $n );

    if ( file_exists ( PAD . 'seq/types/tetrahedral/generated.php' ) ) 
      return in_array ( $n, PADtetrahedral );

    if ( file_exists ( PAD . 'seq/types/tetrahedral/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/tetrahedral/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq tetrahedral, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>