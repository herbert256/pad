<?php

  function padSeqCheckSylvester ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/sylvester/bool.php' ) )
      return padSeqBoolSylvester ( $n );

    if ( file_exists ( PAD . 'seq/types/sylvester/generated.php' ) ) 
      return in_array ( $n, PADsylvester );

    if ( file_exists ( PAD . 'seq/types/sylvester/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/sylvester/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq sylvester, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>