<?php

  function padSeqCheckDivide ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/divide/bool.php" ) )
      return padSeqBoolDivide ( $n );

    $text = padCode ( "{sequence divide, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>