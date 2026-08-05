<?php

  // Membership predicate for pronic: pqBoolPronic($x) is TRUE when x is a pronic (oblong)
  // number i(i+1), the product of two consecutive integers - 0, 2, 6, 12, 20, 30, 42, ... -
  // tested by trying every i up to sqrt(x). The only file in the type, so it is also the
  // generation path and the whole range is filtered through it.

  function pqBoolPronic($x, $p=0) {

    for ($i = 0;
         $i <= (sqrt($x));
         $i++)

        if ($x == $i * ($i + 1))
        return true;

    return false;

  }

?>