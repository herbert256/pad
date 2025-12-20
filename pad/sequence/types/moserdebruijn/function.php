<?php

function pqMoserdebruijn($n)
{

   $S = array();

    $S[0] = 0;
    $S[1] = 1;

    for ( $i = 2; $i <= $n; $i++)
    {

        if ($i % 2 == 0)
        $S[$i] = 4 * $S[$i / 2];

        else
        $S[$i] = 4 * $S[ceil($i/2)] + 1;
    }

    return $S[$n];
}

?>
