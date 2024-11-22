<?php

  function padSeqCheckRecaman ( $f, $n ) {

    if ( file_exists ( 'seq/types/recaman/bool.php' ) )
      return padSeqBoolRecaman ( $n );

    if ( file_exists ( 'seq/types/recaman/generated.php' ) ) 
      return in_array ( $n, PADrecaman );

    if ( file_exists ( 'seq/types/recaman/fixed.php' ) ) {
      $fixed = include 'seq/types/recaman/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq recaman, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>