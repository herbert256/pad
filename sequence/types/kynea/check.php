<?php

  function padSeqCheckKynea ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/kynea/bool.php" ) )
      return padSeqBoolKynea ( $n );

    $text = padCode ( "{sequence kynea, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>