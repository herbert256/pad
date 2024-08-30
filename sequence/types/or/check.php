<?php

  function padSeqCheckOr ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/or/bool.php" ) )
      return padSeqBoolOr ( $n );

    $text = padCode ( "{sequence or, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>