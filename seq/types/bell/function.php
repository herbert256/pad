<?php
// A PHP program to find
// n'th Bell number

// function that returns
// n'th bell number
 function padSeqBell ($n)
{

    $bell[0][0] = 1;
    for ($i = 1; $i <= $n; $i++)
    {
        
        // Explicitly fill for j = 0
        $bell[$i][0] = $bell[$i - 1]
                            [$i - 1];
    
        // Fill for remaining
        // values of j
        for ($j = 1; $j <= $i; $j++)
            $bell[$i][$j] = $bell[$i - 1][$j - 1] +
                                $bell[$i][$j - 1];
    }
    return $bell[$n][0];
}

// This code is contributed by Ajit.
?>