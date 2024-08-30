<?php

  function padSeqCheckLoop ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/loop/bool.php" ) )
      return padSeqBoolLoop ( $n );

    $text = padCode ( "{sequence loop, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>