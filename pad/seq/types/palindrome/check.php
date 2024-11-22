<?php

  function padSeqCheckPalindrome ( $f, $n ) {

    if ( file_exists ( 'seq/types/palindrome/bool.php' ) )
      return padSeqBoolPalindrome ( $n );

    if ( file_exists ( 'seq/types/palindrome/generated.php' ) ) 
      return in_array ( $n, PADpalindrome );

    if ( file_exists ( 'seq/types/palindrome/fixed.php' ) ) {
      $fixed = include 'seq/types/palindrome/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq palindrome, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>