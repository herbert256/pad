<?php

  function padSeqCheckSubstract ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/substract/bool.php" ) )
      return padSeqBoolSubstract ( $n );

    $text = padCode ( "{sequence substract, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>