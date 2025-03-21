<?php

  function padSeqBoolOctahedral( $n ) {

    $text = padCode ( "{seq octahedral, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>