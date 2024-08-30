<?php

  function padSeqCheckPalindrome ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/palindrome/bool.php" ) )
      return padSeqBoolPalindrome ( $n );

    $text = padCode ( "{sequence palindrome, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>