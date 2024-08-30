<?php

  function padSeqCheckPerrin ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/perrin/bool.php" ) )
      return padSeqBoolPerrin ( $n );

    $text = padCode ( "{sequence perrin, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>