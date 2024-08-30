<?php

  function padSeqCheckPerfect ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/perfect/bool.php" ) )
      return padSeqBoolPerfect ( $n );

    $text = padCode ( "{sequence perfect, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>