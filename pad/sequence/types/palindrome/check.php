<?php

  function pqCheckPalindrome ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/palindrome/bool.php' ) )
      return pqBoolPalindrome ( $n, $p );

    if ( file_exists ( 'sequence/types/palindrome/fixed.php' ) ) {
      $fixed = include 'sequence/types/palindrome/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/palindrome/generated.php' ) ) 
      return in_array ( $n, PADpalindrome );

    $text = padCode ( "{sequence palindrome, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>