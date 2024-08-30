<?php

  function padSeqCheckOdd ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/odd/bool.php" ) )
      return padSeqBoolOdd ( $n );

    $text = padCode ( "{sequence odd, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>