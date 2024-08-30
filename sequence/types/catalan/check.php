<?php

  function padSeqCheckCatalan ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/catalan/bool.php" ) )
      return padSeqBoolCatalan ( $n );

    $text = padCode ( "{sequence catalan, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>