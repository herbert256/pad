<?php

  function padSeqCheckPalindrome ( $f, $n ) {

    if ( file_exists ( 'sequence/types/palindrome/bool.php' ) )
      return padSeqBoolPalindrome ( $n );

    if ( file_exists ( 'sequence/types/palindrome/generated.php' ) ) 
      return in_array ( $n, PADpalindrome );

    if ( file_exists ( 'sequence/types/palindrome/fixed.php' ) ) {
      $fixed = include 'sequence/types/palindrome/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence palindrome, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>