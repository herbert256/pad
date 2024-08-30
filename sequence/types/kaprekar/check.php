<?php

  function padSeqCheckKaprekar ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/kaprekar/bool.php" ) )
      return padSeqBoolKaprekar ( $n );

    $text = padCode ( "{sequence kaprekar, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>