<?php

  function padSeqCheckCeil ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/ceil/bool.php" ) )
      return padSeqBoolCeil ( $n );

    $text = padCode ( "{sequence ceil, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>