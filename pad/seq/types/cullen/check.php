<?php

  function padSeqCheckCullen ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/cullen/bool.php' ) )
      return padSeqBoolCullen ( $n );

    if ( file_exists ( PAD . 'seq/types/cullen/generated.php' ) ) 
      return in_array ( $n, PADcullen );

    if ( file_exists ( PAD . 'seq/types/cullen/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/cullen/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq cullen, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>