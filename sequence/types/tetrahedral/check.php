<?php

  function padSeqCheckTetrahedral ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/tetrahedral/bool.php" ) )
      return padSeqBoolTetrahedral ( $n );

    $text = padCode ( "{sequence tetrahedral, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>