<?php

 function pqBell ($n)
{

    $bell[0][0] = 1;
    for ($i = 1; $i <= $n; $i++)
    {

        $bell[$i][0] = $bell[$i - 1]
                            [$i - 1];

        for ($j = 1; $j <= $i; $j++)
            $bell[$i][$j] = $bell[$i - 1][$j - 1] +
                                $bell[$i][$j - 1];
    }
    return $bell[$n][0];
}

?>
