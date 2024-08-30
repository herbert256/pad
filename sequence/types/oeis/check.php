<?php

  function padSeqCheckOeis ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/oeis/bool.php" ) )
      return padSeqBoolOeis ( $n );

    $text = padCode ( "{sequence oeis, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>