<?php

  function padSeqCheckEven ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/even/bool.php" ) )
      return padSeqBoolEven ( $n );

    $text = padCode ( "{sequence even, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>