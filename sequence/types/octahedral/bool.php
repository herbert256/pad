<?php

  function padSeqBoolOctahedral( $n ) {

    $text = padCode ( "{sequence octahedral, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>