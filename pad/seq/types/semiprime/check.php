<?php

  function padSeqCheckSemiprime ( $f, $n ) {

    if ( file_exists ( 'seq/types/semiprime/bool.php' ) )
      return padSeqBoolSemiprime ( $n );

    if ( file_exists ( 'seq/types/semiprime/generated.php' ) ) 
      return in_array ( $n, PADsemiprime );

    if ( file_exists ( 'seq/types/semiprime/fixed.php' ) ) {
      $fixed = include 'seq/types/semiprime/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq semiprime, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>