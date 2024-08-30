<?php

  function padSeqCheckNewmanConway ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/newmanConway/bool.php" ) )
      return padSeqBoolNewmanConway ( $n );

    $text = padCode ( "{sequence newmanConway, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>