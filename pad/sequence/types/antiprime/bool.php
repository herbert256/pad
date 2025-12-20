<?php

  function pqBoolAntiprime ($n, $p=0) {

    $init = pqBoolAntiprimeDivisors ($n);

    for ($i = 1; $i < $n; $i++)
        if (pqBoolAntiprimeDivisors($i) >= $init)
            return false;

    return true;

  }

  function pqBoolAntiprimeDivisors ($a) {

    if ($a == 1)
        return 1;

    $f = 2;

    for ($i = 2; $i * $i <= $a; $i++)
        if ($a % $i == 0)
            if ($i * $i <> $a)
                $f += 2;
            else
                $f++;

    return $f;
}

?>