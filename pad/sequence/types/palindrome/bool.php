<?php

  // Membership predicate for palindrome: pqBoolPalindrome($n) is TRUE when n reads the same
  // backwards, compared against padTypeReverse() from lib/sequence.php.
  //
  // Also the generation path, since it is the only file pqBuild() finds here: the range is
  // walked and every palindrome kept - 1 to 9, 11, 22, ..., 99, 101, 111, ...

  function pqBoolPalindrome ($n, $p=0) {

    if ( $n == padTypeReverse($n) )
      return TRUE;
    else
      return FALSE;

  }

?>