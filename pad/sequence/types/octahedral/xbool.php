<?php

  function padSeqBoolOctahedral( $n, $p=0 ) {

    $text = padCode ( "{sequence octahedral, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>