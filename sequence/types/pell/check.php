<?php

  function padSeqCheckPell ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/pell/bool.php" ) )
      return padSeqBoolPell ( $n );

    $text = padCode ( "{sequence pell, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>