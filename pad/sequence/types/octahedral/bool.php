<?php

  function padSeqBoolOctahedral( $n, $p=0 ) {

    $text = padCode ( "{seq octahedral, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>