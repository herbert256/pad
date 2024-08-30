<?php

  function padSeqCheckTriangular ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/triangular/bool.php" ) )
      return padSeqBoolTriangular ( $n );

    $text = padCode ( "{sequence triangular, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>