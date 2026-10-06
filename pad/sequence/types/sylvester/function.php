<?php

  // Function build for sylvester: pqSylvester($n) is the nth term of Sylvester's sequence,
  // a(1) = 2 with each term the product of all earlier terms plus one - 2, 3, 7, 43, 1807,
  // 3263443, 10650056950807, ...
  //
  // $a carries the running product of the terms so far, so the next term is always $a + 1.
  // The terms roughly square at every step and the seventh already needs 44 bits: the
  // eighth overflows into a float beyond PHP_INT_MAX, at which point build/one.php ends the
  // build, so a sequence of these stops after seven terms. Indentation notwithstanding,
  // only the return is governed by the i == n test.

  function pqSylvester ($n) {

    $a = 1;

    $ans = 2;

    for ($i = 1; $i <= $n; $i++)
    {

      if ($i==$n)
        return $ans;
        $ans = $a * $ans;
        $a = $ans;
        $ans = $ans + 1;

        // From the eighth term on the product is a float out of the integer range, which ends
        // the build: going on to n made a far position - from=1000000000000 - loop for ever.

        if ( is_float ( $ans ) )
          return $ans;
    }

    return $ans;

  }

?>
