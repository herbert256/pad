<?php

  function padSeqCheckMersenne ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/mersenne/bool.php" ) )
      return padSeqBoolMersenne ( $n );

    $text = padCode ( "{sequence mersenne, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>