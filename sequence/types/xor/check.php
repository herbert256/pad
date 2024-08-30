<?php

  function padSeqCheckXor ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/xor/bool.php" ) )
      return padSeqBoolXor ( $n );

    $text = padCode ( "{sequence xor, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>