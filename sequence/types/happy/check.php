<?php

  function padSeqCheckHappy ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/happy/bool.php" ) )
      return padSeqBoolHappy ( $n );

    $text = padCode ( "{sequence happy, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>