<?php

function pqBinomialCoeff($n, $k)
{
    $res = 1;

    if ($k > $n - $k)
        $k = $n - $k;

    for ($i = 0; $i < $k; ++$i)
    {
        $res *= ($n - $i);
        $res = floor($res / ($i + 1));
    }

    return $res;
}

function pqCatalan($n)
{

    $c = pqBinomialCoeff(2 * ($n), $n);

    return floor($c / ($n + 1));
}

?>