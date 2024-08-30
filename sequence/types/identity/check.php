<?php

  function padSeqCheckIdentity ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/identity/bool.php" ) )
      return padSeqBoolIdentity ( $n );

    $text = padCode ( "{sequence identity, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>