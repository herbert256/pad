<?php

  function pqSylvester ($n) {

    $N = 1000000007;

    $a = 1;

    $ans = 2;

    for ($i = 1; $i <= $n; $i++)
    {

      if ($i==$n)
        return $ans;
        $ans = (($a % $N) * ($ans % $N)) % $N;
        $a = $ans;
        $ans = ($ans + 1) % $N;
    }

    return $ans;

  }

?>