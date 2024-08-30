<?php

  function padSeqCheckNand ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/nand/bool.php" ) )
      return padSeqBoolNand ( $n );

    $text = padCode ( "{sequence nand, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>