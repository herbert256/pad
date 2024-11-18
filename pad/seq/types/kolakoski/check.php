<?php

  function padSeqCheckKolakoski ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/kolakoski/bool.php' ) )
      return padSeqBoolKolakoski ( $n );

    if ( file_exists ( PAD . 'seq/types/kolakoski/generated.php' ) ) 
      return in_array ( $n, PADkolakoski );

    if ( file_exists ( PAD . 'seq/types/kolakoski/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/kolakoski/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq kolakoski, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>