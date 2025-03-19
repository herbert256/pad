<?php

  function padSeqCheckKolakoski ( $f, $n ) {

    if ( file_exists ( 'seq/types/kolakoski/bool.php' ) )
      return padSeqBoolKolakoski ( $n );

    if ( file_exists ( 'seq/types/kolakoski/generated.php' ) ) 
      return in_array ( $n, PADkolakoski );

    if ( file_exists ( 'seq/types/kolakoski/fixed.php' ) ) {
      $fixed = include 'seq/types/kolakoski/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence kolakoski, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>