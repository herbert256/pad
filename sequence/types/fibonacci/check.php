<?php

  function padSeqCheckFibonacci ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/fibonacci/bool.php" ) )
      return padSeqBoolFibonacci ( $n );

    $text = padCode ( "{sequence fibonacci, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>